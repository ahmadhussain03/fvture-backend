<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class AppInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install {--force : Force installation even if already installed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the application: create S3 bucket, seed permissions, roles, and users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting application installation...');

        // Check if already installed (unless force flag is used)
        if (!$this->option('force') && $this->isAlreadyInstalled()) {
            $this->warn('Application appears to be already installed.');
            if (!$this->confirm('Do you want to continue anyway?')) {
                $this->info('Installation cancelled.');
                return 0;
            }
        }

        try {
            // Step 1: Create S3 bucket
            $this->createS3Bucket($this->option('force'));

            // Step 2: Run database migrations
            $this->runMigrations();

            // Step 3: Seed permissions and roles
            $this->seedPermissionsAndRoles();

            // Step 4: Seed users
            $this->seedUsers();

            $this->info('✅ Application installation completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Installation failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Check if the application is already installed
     */
    private function isAlreadyInstalled(): bool
    {
        // Check if roles exist in database
        try {
            $roleCount = \Spatie\Permission\Models\Role::count();
            return $roleCount > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create S3 bucket if it doesn't exist
     */
    private function createS3Bucket(bool $force = false): void
    {
        $this->info('📦 Checking S3 bucket...');

        $bucketName = config('filesystems.disks.s3.bucket');
        $endpoint = config('filesystems.disks.s3.endpoint');
        $usePathStyleEndpoint = config('filesystems.disks.s3.use_path_style_endpoint', false);

        // Skip S3 bucket creation if using default Laravel bucket name (likely not configured)
        if ($bucketName === 'laravel' && !$endpoint) {
            $this->warn("⚠️ Using default S3 bucket name 'laravel'. Please configure your S3 settings in .env file.");
            $this->info("✅ Skipping S3 bucket creation. Please configure S3 settings manually.");
            return;
        }

        try {
            // Create S3 client configuration with improved Minio support
            $s3Config = [
                'version' => 'latest',
                'region' => config('filesystems.disks.s3.region', 'us-east-1'),
            ];

            // Add credentials if available
            if (config('filesystems.disks.s3.key') && config('filesystems.disks.s3.secret')) {
                $s3Config['credentials'] = [
                    'key' => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ];
            }

            // Add endpoint for Minio or other S3-compatible services
            if ($endpoint) {
                $s3Config['endpoint'] = $endpoint;
                $s3Config['use_path_style_endpoint'] = $usePathStyleEndpoint;
            }

            // Add additional options for better compatibility
            $s3Config['options'] = [
                'ServerSideEncryption' => 'AES256',
            ];

            $s3Client = new S3Client($s3Config);

            // Check if bucket exists
            $bucketExists = false;
            try {
                $s3Client->headBucket(['Bucket' => $bucketName]);
                $bucketExists = true;
            } catch (AwsException $e) {
                if ($e->getAwsErrorCode() !== 'NotFound') {
                    throw $e;
                }
                // Bucket doesn't exist
            }

            if ($bucketExists) {
                if ($force) {
                    $this->info("🔄 Force mode: Deleting existing S3 bucket '{$bucketName}'...");
                    
                    // Delete all objects in the bucket first
                    try {
                        $objects = $s3Client->listObjectsV2(['Bucket' => $bucketName]);
                        if (isset($objects['Contents'])) {
                            foreach ($objects['Contents'] as $object) {
                                $s3Client->deleteObject([
                                    'Bucket' => $bucketName,
                                    'Key' => $object['Key']
                                ]);
                            }
                        }
                        
                        // Delete the bucket
                        $s3Client->deleteBucket(['Bucket' => $bucketName]);
                        $this->info("✅ S3 bucket '{$bucketName}' deleted successfully.");
                    } catch (AwsException $e) {
                        throw new \Exception("Failed to delete S3 bucket: " . $e->getMessage());
                    }
                } else {
                    $this->info("✅ S3 bucket '{$bucketName}' already exists.");
                    return;
                }
            }

            // Create bucket
            $this->info("🔄 Creating S3 bucket '{$bucketName}'...");
            $createParams = ['Bucket' => $bucketName];
            
            // Set ACL to public for both AWS S3 and Minio
            $createParams['ACL'] = 'public';

            // For Minio, we might need to specify the region in the CreateBucketConfiguration
            if ($endpoint) {
                $createParams['CreateBucketConfiguration'] = [
                    'LocationConstraint' => config('filesystems.disks.s3.region', 'us-east-1')
                ];
            }

            $s3Client->createBucket($createParams);

            // Set bucket policy to public read for Minio
            if ($endpoint) {
                try {
                    $publicPolicy = [
                        'Version' => '2012-10-17',
                        'Statement' => [
                            [
                                'Effect' => 'Allow',
                                'Principal' => '*',
                                'Action' => 's3:GetObject',
                                'Resource' => "arn:aws:s3:::{$bucketName}/*"
                            ]
                        ]
                    ];
                    
                    $s3Client->putBucketPolicy([
                        'Bucket' => $bucketName,
                        'Policy' => json_encode($publicPolicy)
                    ]);
                    
                    $this->info("✅ Minio bucket policy set to public read.");
                } catch (\Exception $e) {
                    $this->warn("⚠️ Could not set bucket policy: " . $e->getMessage());
                }
            }

            $bucketType = $endpoint ? 'Minio' : 'AWS S3';
            $this->info("✅ {$bucketType} bucket '{$bucketName}' created successfully (public).");

        } catch (AwsException $e) {
            if ($e->getAwsErrorCode() === 'BucketAlreadyOwnedByYou') {
                $this->info("✅ S3 bucket '{$bucketName}' already exists and is owned by you.");
            } elseif ($e->getAwsErrorCode() === 'BucketAlreadyExists') {
                $this->info("✅ S3 bucket '{$bucketName}' already exists.");
            } else {
                $bucketType = $endpoint ? 'Minio' : 'AWS S3';
                throw new \Exception("Failed to create {$bucketType} bucket: " . $e->getMessage());
            }
        } catch (\Exception $e) {
            $bucketType = $endpoint ? 'Minio' : 'AWS S3';
            throw new \Exception("{$bucketType} configuration error: " . $e->getMessage());
        }
    }

    /**
     * Run database migrations
     */
    private function runMigrations(): void
    {
        $this->info('🗄️ Running database migrations...');
        
        Artisan::call('migrate', ['--force' => true]);
        
        $this->info('✅ Database migrations completed.');
    }

    /**
     * Seed permissions and roles
     */
    private function seedPermissionsAndRoles(): void
    {
        $this->info('🔐 Seeding permissions and roles...');
        
        Artisan::call('db:seed', [
            '--class' => 'PermissionRoleSeeder',
            '--force' => true
        ]);
        
        $this->info('✅ Permissions and roles seeded successfully.');
    }

    /**
     * Seed users
     */
    private function seedUsers(): void
    {
        $this->info('👥 Seeding users...');
        
        Artisan::call('db:seed', [
            '--class' => 'UserSeeder',
            '--force' => true
        ]);
        
        $this->info('✅ Users seeded successfully.');
    }
}