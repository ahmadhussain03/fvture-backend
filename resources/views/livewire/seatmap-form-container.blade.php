<div
    x-data="{
        mapWidth: 700,
        mapHeight: 450,
        formWidth: 700,
        updateFromInputs() {
            const update = () => {
                const widthInput = document.getElementById('form.map_width');
                const heightInput = document.getElementById('form.map_height');
                const form = document.querySelector('.fi-form');
                if (form) {
                    this.formWidth = form.offsetWidth;
                }
                if (widthInput) {
                    this.mapWidth = parseInt(widthInput.value) || this.formWidth;
                    widthInput.max = this.formWidth;
                }
                if (heightInput) {
                    this.mapHeight = parseInt(heightInput.value) || 450;
                }
                console.log('DEBUG Alpine update:', {
                    mapWidth: this.mapWidth,
                    mapHeight: this.mapHeight,
                    formWidth: this.formWidth,
                    widthInput,
                    heightInput,
                    form
                });
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
        :style="`width: ${mapWidth}px; height: ${mapHeight}px;`"
        class="fi-section rounded-xl bg-custom-500/5 dark:bg-custom-500/5 flex items-center justify-center transition-all duration-300"
    >
        <!-- Custom content goes here -->
    </div>
</div>
