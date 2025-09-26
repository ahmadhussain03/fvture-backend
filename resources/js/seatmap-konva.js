import Konva from "konva";

window.initSeatmapKonva = function (containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    function initializeKonva() {
        // Remove previous stage if exists
        if (container._konvaStage) {
            console.log("[Konva] Destroying previous stage");
            container._konvaStage.destroy();
        }
        // Get container size
        const width = container.offsetWidth;
        const height = container.offsetHeight;
        console.log(
            "[Konva] Initializing canvas:",
            containerId,
            "Node:",
            container
        );
        console.log("[Konva] Canvas size:", width, height);
        if (height === 0) {
            console.log("[Konva] Skipping initialization due to zero height");
            return;
        }
        const stage = new Konva.Stage({
            container: containerId,
            width: width,
            height: height,
        });
        const layer = new Konva.Layer();
        stage.add(layer);
        // Store stage and layer reference for cleanup and event use
        container._konvaStage = stage;
        container._konvaLayer = layer;

        // Remove previous event listener if any
        if (container._placeTableListener) {
            window.removeEventListener(
                "place-table",
                container._placeTableListener
            );
            console.log("[Konva] Removed previous place-table listener");
        }
        // Attach a new event listener that always uses the latest layer
        container._placeTableListener = function () {
            console.log("[Konva] place-table event fired");
            const width = container.offsetWidth;
            const height = container.offsetHeight;
            const circle = new Konva.Circle({
                x: width / 2,
                y: height / 2,
                radius: Math.min(width, height) / 6,
                fill: "dodgerblue",
                stroke: "red",
                strokeWidth: 5,
                draggable: true,
            });
            layer.add(circle);
            layer.draw();
            console.log(
                "[Konva] Circle added to layer:",
                layer,
                "Stage:",
                stage
            );
        };
        window.addEventListener("place-table", container._placeTableListener);
        console.log("[Konva] place-table listener attached");
    }

    // Initialize when height is non-zero
    if (container.offsetHeight > 0) {
        initializeKonva();
    } else {
        // Wait for height to be set
        const heightCheck = setInterval(() => {
            if (container.offsetHeight > 0) {
                clearInterval(heightCheck);
                initializeKonva();
            }
        }, 50);
    }

    // DOM mutation debug and auto re-init
    const observer = new MutationObserver((mutations) => {
        console.log("[Konva] Mutation detected:", mutations);
        // Log canvas size on every mutation
        const canvasDiv = document.getElementById(containerId);
        if (canvasDiv) {
            console.log(
                "[Konva] Canvas size after mutation:",
                canvasDiv.offsetWidth,
                canvasDiv.offsetHeight
            );
            // If canvas is missing its <canvas> child, re-initialize
            if (!canvasDiv.querySelector("canvas")) {
                console.log("[Konva] Canvas missing, re-initializing...");
                window.initSeatmapKonva(containerId);
            }
        }
    });
    observer.observe(container.parentNode, { childList: true, subtree: true });
    container._konvaMutationObserver = observer;
};
