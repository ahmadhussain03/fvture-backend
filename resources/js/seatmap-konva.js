import Konva from "konva";

window.initSeatmapKonva = function (containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    // Remove previous stage if exists
    if (container._konvaStage) {
        container._konvaStage.destroy();
    }
    // Get container size
    const width = container.offsetWidth;
    const height = container.offsetHeight;
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
    }
    // Attach a new event listener that always uses the latest layer
    container._placeTableListener = function () {
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
    };
    window.addEventListener("place-table", container._placeTableListener);
};
