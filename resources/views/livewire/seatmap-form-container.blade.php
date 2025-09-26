<div
    x-data="{
        mapWidth: 700,
        mapHeight: 450,
        maxComponentWidth: 700,
        formWidth: 700,
        updateFromInputs() {
            const update = () => {
                const widthInput = document.getElementById('form.map_width');
                const heightInput = document.getElementById('form.map_height');
                const component = document.querySelector('.fi-sc-component');
                if (component) {
                    this.maxComponentWidth = component.offsetWidth;
                }
                if (widthInput) {
                    // Set max to component width
                    widthInput.max = this.maxComponentWidth;
                    // Clamp value to max
                    this.mapWidth = Math.min(parseInt(widthInput.value) || this.maxComponentWidth, this.maxComponentWidth);
                }
                if (heightInput) {
                    this.mapHeight = parseInt(heightInput.value) || 450;
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
