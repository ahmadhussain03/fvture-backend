// resources/js/seatmap-konva.js
import Konva from "konva";

window.initSeatmapKonva = function (
    containerId,
    width = 700,
    height = 450,
    backgroundImage = null
) {
    const stage = new Konva.Stage({
        container: containerId,
        width: width,
        height: height,
    });
    const layer = new Konva.Layer();
    stage.add(layer);

    if (backgroundImage) {
        const imageObj = new window.Image();
        imageObj.onload = function () {
            const bg = new Konva.Image({
                x: 0,
                y: 0,
                image: imageObj,
                width: width,
                height: height,
            });
            layer.add(bg);
            layer.draw();
        };
        imageObj.src = backgroundImage;
    }

    // Example: Add a draggable table (circle)
    const table = new Konva.Circle({
        x: width / 2,
        y: height / 2,
        radius: 40,
        fill: "#1976d2",
        stroke: "black",
        strokeWidth: 2,
        draggable: true,
    });
    layer.add(table);
    layer.draw();

    // You can expose stage/layer for further manipulation if needed
    return { stage, layer };
};
