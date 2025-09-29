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
    <!-- Hidden field for full Club Table data -->
    <input type="hidden" name="club_tables_json" id="club_tables_json" value="{{ old('club_tables_json', $get('club_tables_json') ?? '') }}">
    
    <!-- Hidden field for seatmap tables data -->
    <input type="hidden" name="seatmap_tables_json" id="seatmap_tables_json" value="{{ old('seatmap_tables_json', $get('seatmap_tables_json') ?? '') }}">

    <!-- Debug button removed, now updates in real time -->
    <!-- Debug labels removed -->
    <div
        :style="`width: ${mapWidth}px; height: ${mapHeight}px; ${backgroundImage ? `background-image: url('${backgroundImage}'); background-size: cover; background-position: center;` : ''}`"
        class="fi-section rounded-xl bg-custom-500/5 dark:bg-custom-500/5 flex items-center justify-center transition-all duration-300"
    >
        <!-- Konva.js Canvas -->
    <div id="konva-seatmap-canvas" style="width: 100%; height: 100%; background: transparent;"></div>
        <script>
            // Resize observer to keep Konva canvas in sync with container size
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('konva-seatmap-canvas');
                function resizeKonva() {
                    if (container && container._konvaStage) {
                        const width = container.offsetWidth;
                        const height = container.offsetHeight;
                        container._konvaStage.width(width);
                        container._konvaStage.height(height);
                        // Optionally, reposition/redraw objects here if needed
                        const layer = container._konvaStage.children[0];
                        if (layer && layer.children && layer.children[0]) {
                            // Center the circle again
                            const circle = layer.children[0];
                            circle.x(width/2);
                            circle.y(height/2);
                            circle.radius(Math.min(width, height) / 6);
                            layer.draw();
                        }
                    }
                }
                // Observe size changes
                if (window.ResizeObserver) {
                    const ro = new ResizeObserver(resizeKonva);
                    ro.observe(container);
                } else {
                    window.addEventListener('resize', resizeKonva);
                }
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.initSeatmapKonva('konva-seatmap-canvas');
            });
        </script>
    </div>
</div>
