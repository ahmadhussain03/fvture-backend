<div
    x-data="{
        mapWidth: 700,
        mapHeight: 450,
        maxComponentWidth: 700,
    backgroundImage: null,
    imageAspectRatio: null,
        updateFromInputs() {
            const update = () => {
                // Find the image upload input or its parent container
                const imageInput = document.querySelector('input.filepond--browser');
                let maxWidth = 700;
                if (imageInput && imageInput.parentElement) {
                    // Use parent container's width for max width
                    maxWidth = imageInput.parentElement.offsetWidth;
                } else {
                    // Fallback to form section/container width
                    const section = document.querySelector('.fi-section');
                    if (section) {
                        maxWidth = section.offsetWidth;
                    }
                }
                this.maxComponentWidth = maxWidth;

                const widthInput = document.getElementById('form.map_width');
                const heightInput = document.getElementById('form.map_height');

                if (widthInput) {
                    widthInput.max = this.maxComponentWidth;
                    // If input is empty, set to max width
                    if (!widthInput.value) {
                        widthInput.value = this.maxComponentWidth;
                    }
                    this.mapWidth = Math.min(parseInt(widthInput.value) || this.maxComponentWidth, this.maxComponentWidth);
                } else {
                    this.mapWidth = this.maxComponentWidth;
                }
                if (heightInput) {
                    // If image aspect ratio is set, always use calculated height
                    if (this.imageAspectRatio) {
                        const newHeight = Math.round(this.mapWidth / this.imageAspectRatio);
                        this.mapHeight = newHeight;
                        heightInput.value = newHeight;
                    } else {
                        this.mapHeight = parseInt(heightInput.value) || 450;
                    }
                }
            };
            update();

            // Listen for input changes directly on the form inputs
            const widthInput = document.getElementById('form.map_width');
            const heightInput = document.getElementById('form.map_height');
            if (widthInput) {
                widthInput.addEventListener('input', update);
            }
            if (heightInput) {
                heightInput.addEventListener('input', update);
            }
            // Listen for window resize to update max width
            window.addEventListener('resize', update);
            // Listen for image upload changes for live preview
            const imageInput = document.querySelector('input.filepond--browser');
            if (imageInput) {
                imageInput.addEventListener('change', (e) => {
                    if (e.target.files && e.target.files[0]) {
                        const file = e.target.files[0];
                        this.backgroundImage = URL.createObjectURL(file);
                        // Read image aspect ratio
                        const img = new Image();
                        img.onload = () => {
                            this.imageAspectRatio = img.naturalWidth / img.naturalHeight;
                            // Calculate new height based on current width
                            const newHeight = Math.round(this.mapWidth / this.imageAspectRatio);
                            this.mapHeight = newHeight;
                            // Update Map Height input
                            const heightInput = document.getElementById('form.map_height');
                            if (heightInput) {
                                heightInput.value = newHeight;
                            }
                        };
                        img.src = this.backgroundImage;
                    } else {
                        this.backgroundImage = null;
                        this.imageAspectRatio = null;
                    }
                });
            }
            // MutationObserver for Livewire re-renders
            const observer = new MutationObserver(() => {
                // Re-attach listeners after DOM changes
                const widthInput = document.getElementById('form.map_width');
                const heightInput = document.getElementById('form.map_height');
                if (widthInput) {
                    widthInput.removeEventListener('input', update);
                    widthInput.addEventListener('input', update);
                }
                if (heightInput) {
                    heightInput.removeEventListener('input', update);
                    heightInput.addEventListener('input', update);
                }
                // Re-attach image change listener
                const imageInput = document.querySelector('input.filepond--browser');
                if (imageInput) {
                    imageInput.removeEventListener('change', this.imageChangeListener);
                    imageInput.addEventListener('change', (e) => {
                        if (e.target.files && e.target.files[0]) {
                            const file = e.target.files[0];
                            this.backgroundImage = URL.createObjectURL(file);
                            // Read image aspect ratio
                            const img = new Image();
                            img.onload = () => {
                                this.imageAspectRatio = img.naturalWidth / img.naturalHeight;
                                // Calculate new height based on current width
                                const newHeight = Math.round(this.mapWidth / this.imageAspectRatio);
                                this.mapHeight = newHeight;
                                // Update Map Height input
                                const heightInput = document.getElementById('form.map_height');
                                if (heightInput) {
                                    heightInput.value = newHeight;
                                }
                            };
                            img.src = this.backgroundImage;
                        } else {
                            this.backgroundImage = null;
                            this.imageAspectRatio = null;
                        }
                    });
                }
                update();
            });
            observer.observe(document.body, { childList: true, subtree: true });
        }
    }"
    x-init="updateFromInputs()"
    class="w-full"
>
    <!-- Debug button removed, now updates in real time -->
    <!-- Debug labels removed -->
    <div
        :style="`width: ${mapWidth}px; height: ${mapHeight}px; background: #f5f5f5;`"
        class="fi-section rounded-xl bg-custom-500/5 dark:bg-custom-500/5 flex items-center justify-center transition-all duration-300"
    >
        <div id="seatmap-konva-canvas" style="width: 100%; height: 100%;"></div>
    </div>
    <script type="module">
        import '/resources/js/seatmap-konva.js';
        document.addEventListener('DOMContentLoaded', () => {
            window.initSeatmapKonva('seatmap-konva-canvas', 700, 450);
        });
    </script>
</div>
