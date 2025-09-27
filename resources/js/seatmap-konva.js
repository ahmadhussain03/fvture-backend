import Konva from "konva";

window.initSeatmapKonva = function (containerId) {
    // Helper to serialize all table objects to JSON for backend
    function serializeTablesToJson() {
        const container = document.getElementById(containerId);
        if (!container || !container._konvaLayer) return;
        const layer = container._konvaLayer;
        const tables = layer
            .getChildren()
            .filter((obj) => obj.className === "Image")
            .map((img) => ({
                club_table_id: img.attrs.club_table_id || null,
                x: img.x(),
                y: img.y(),
                width: img.width(),
                height: img.height(),
                number: img.attrs.seat_number || null,
            }));
        const hiddenField = document.getElementById("form.seatmap_tables_json");
        if (hiddenField) {
            hiddenField.value = JSON.stringify(tables);
        }
    }
    window.serializeTablesToJson = serializeTablesToJson;

    // Attach to form submit
    document.addEventListener(
        "submit",
        function (e) {
            serializeTablesToJson();
        },
        true
    );
    // Disable and clear custom X/Y fields on initial load
    const customXInputInit = document.getElementById("form.custom_table_x");
    const customYInputInit = document.getElementById("form.custom_table_y");
    if (customXInputInit) {
        customXInputInit.disabled = true;
        customXInputInit.value = "";
    }
    if (customYInputInit) {
        customYInputInit.disabled = true;
        customYInputInit.value = "";
    }
    // Disable and clear custom width/height fields on initial load
    const customWidthInputInit = document.getElementById(
        "form.custom_table_width"
    );
    const customHeightInputInit = document.getElementById(
        "form.custom_table_height"
    );
    if (customWidthInputInit) {
        customWidthInputInit.disabled = true;
        customWidthInputInit.value = "";
    }
    if (customHeightInputInit) {
        customHeightInputInit.disabled = true;
        customHeightInputInit.value = "";
    }
    const container = document.getElementById(containerId);
    if (!container) return;

    function initializeKonva() {
        // Enable deleting selected table(s) with Delete or Backspace key
        function removeSelectedTables() {
            if (multiSelectedKonvaImgs && multiSelectedKonvaImgs.length > 0) {
                multiSelectedKonvaImgs.forEach((img) => {
                    img.destroy();
                });
                multiSelectedKonvaImgs = [];
                window.selectedKonvaImg = null;
                // Remove any transformer
                let oldTransformer = layer.findOne(".table-transformer");
                if (oldTransformer) {
                    oldTransformer.destroy();
                }
                // Optionally, clear/disable input fields
                const customWidthInput = document.getElementById(
                    "form.custom_table_width"
                );
                const customHeightInput = document.getElementById(
                    "form.custom_table_height"
                );
                const customXInput = document.getElementById(
                    "form.custom_table_x"
                );
                const customYInput = document.getElementById(
                    "form.custom_table_y"
                );
                if (customWidthInput) {
                    customWidthInput.value = "";
                    customWidthInput.disabled = true;
                }
                if (customHeightInput) {
                    customHeightInput.value = "";
                    customHeightInput.disabled = true;
                }
                if (customXInput) {
                    customXInput.value = "";
                    customXInput.disabled = true;
                }
                if (customYInput) {
                    customYInput.value = "";
                    customYInput.disabled = true;
                }
                layer.draw();
            }
        }
        // ...existing code...

        // Listen for Delete/Backspace key to remove selected tables
        // Use capture to ensure it works even if focus is on input
        document.addEventListener("keydown", function handleDeleteKey(e) {
            if (
                (e.key === "Delete" || e.key === "Backspace") &&
                multiSelectedKonvaImgs &&
                multiSelectedKonvaImgs.length > 0
            ) {
                // Prevent default only if not typing in an input/textarea
                const tag = document.activeElement.tagName.toLowerCase();
                if (tag !== "input" && tag !== "textarea") {
                    e.preventDefault();
                    removeSelectedTables();
                }
            }
        });
        // Multi-select state
        let selectionRect, selectionStart, selectionEnd;
        let multiSelectedKonvaImgs = [];
        // Remove previous stage if exists
        if (container._konvaStage) {
            container._konvaStage.destroy();
        }
        // Get container size
        const width = container.offsetWidth;
        const height = container.offsetHeight;
        if (height === 0) {
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

        // Helper to update highlight - MOVED HERE to be accessible by both drag-select and place-table
        function highlightImage(img) {
            // Get input field references
            const customWidthInput = document.getElementById(
                "form.custom_table_width"
            );
            const customHeightInput = document.getElementById(
                "form.custom_table_height"
            );
            const customXInput = document.getElementById("form.custom_table_x");
            const customYInput = document.getElementById("form.custom_table_y");

            // Remove any existing transformer
            let oldTransformer = layer.findOne(".table-transformer");
            if (oldTransformer) {
                oldTransformer.destroy();
            }

            // Remove highlight from all (no stroke/shadow)
            layer.getChildren().forEach((child) => {
                child.strokeEnabled && child.strokeEnabled(false);
                child.shadowEnabled && child.shadowEnabled(false);
            });

            // Always use transformer for selection indication
            if (Array.isArray(img)) {
                window.selectedKonvaImg = img[0] || null;
                multiSelectedKonvaImgs = img;
                if (multiSelectedKonvaImgs.length > 0) {
                    const transformer = new Konva.Transformer({
                        nodes: multiSelectedKonvaImgs,
                        name: "table-transformer",
                        enabledAnchors: [
                            "top-left",
                            "top-right",
                            "bottom-left",
                            "bottom-right",
                            "middle-left",
                            "middle-right",
                            "top-center",
                            "bottom-center",
                        ],
                        boundBoxFunc: function (oldBox, newBox) {
                            if (newBox.width < 10 || newBox.height < 10) {
                                return oldBox;
                            }
                            return newBox;
                        },
                    });
                    layer.add(transformer);
                    layer.draw();
                }
            } else {
                window.selectedKonvaImg = img;
                multiSelectedKonvaImgs = [img];
                const transformer = new Konva.Transformer({
                    nodes: [img],
                    name: "table-transformer",
                    enabledAnchors: [
                        "top-left",
                        "top-right",
                        "bottom-left",
                        "bottom-right",
                        "middle-left",
                        "middle-right",
                        "top-center",
                        "bottom-center",
                    ],
                    boundBoxFunc: function (oldBox, newBox) {
                        if (newBox.width < 10 || newBox.height < 10) {
                            return oldBox;
                        }
                        return newBox;
                    },
                });
                layer.add(transformer);
                layer.draw();
            }

            // Enable fields when an object is selected
            if (customWidthInput) customWidthInput.disabled = false;
            if (customHeightInput) customHeightInput.disabled = false;
            if (customXInput) customXInput.disabled = false;
            if (customYInput) customYInput.disabled = false;

            // Set input fields: only show value if all selected have the same value
            function allSame(getter) {
                if (multiSelectedKonvaImgs.length === 0) return "";
                const first = getter(multiSelectedKonvaImgs[0]);
                return multiSelectedKonvaImgs.every(
                    (img) => getter(img) === first
                )
                    ? Math.round(first)
                    : "";
            }

            if (customWidthInput)
                customWidthInput.value = allSame((img) => img.width());
            if (customHeightInput)
                customHeightInput.value = allSame((img) => img.height());
            if (customXInput) customXInput.value = allSame((img) => img.x());
            if (customYInput) customYInput.value = allSame((img) => img.y());
        }

        // ADD DRAG-TO-SELECT EVENT LISTENERS HERE (immediately after stage creation)
        stage.on("mousedown touchstart", (e) => {
            // Only start selection if not clicking on a shape
            if (e.target === stage) {
                // Always clear selection and transformer on click/tap on empty canvas
                multiSelectedKonvaImgs = [];
                window.selectedKonvaImg = null;

                // Remove any transformer
                let oldTransformer = layer.findOne(".table-transformer");
                if (oldTransformer) {
                    oldTransformer.destroy();
                }

                // Clear input fields and disable
                const customWidthInput = document.getElementById(
                    "form.custom_table_width"
                );
                const customHeightInput = document.getElementById(
                    "form.custom_table_height"
                );
                const customXInput = document.getElementById(
                    "form.custom_table_x"
                );
                const customYInput = document.getElementById(
                    "form.custom_table_y"
                );

                if (customWidthInput) {
                    customWidthInput.value = "";
                    customWidthInput.disabled = true;
                }
                if (customHeightInput) {
                    customHeightInput.value = "";
                    customHeightInput.disabled = true;
                }
                if (customXInput) {
                    customXInput.value = "";
                    customXInput.disabled = true;
                }
                if (customYInput) {
                    customYInput.value = "";
                    customYInput.disabled = true;
                }

                // Remove highlight from all
                layer.getChildren().forEach((child) => {
                    child.strokeEnabled && child.strokeEnabled(false);
                    child.shadowEnabled && child.shadowEnabled(false);
                });
                layer.draw();

                // Start selection rectangle for drag-to-select
                selectionStart = stage.getPointerPosition();
                if (!selectionRect) {
                    selectionRect = new Konva.Rect({
                        fill: "rgba(0,161,255,0.2)",
                        visible: false,
                    });
                    layer.add(selectionRect);
                }
                selectionRect.setAttrs({
                    x: selectionStart.x,
                    y: selectionStart.y,
                    width: 0,
                    height: 0,
                    visible: true,
                });
                layer.draw();

                // Mark that we are starting a drag
                stage._isDragSelecting = true;
            }
        });

        stage.on("mousemove touchmove", (e) => {
            if (!selectionRect || !selectionRect.visible()) return;
            selectionEnd = stage.getPointerPosition();
            selectionRect.width(selectionEnd.x - selectionStart.x);
            selectionRect.height(selectionEnd.y - selectionStart.y);
            layer.batchDraw();
        });

        stage.on("mouseup touchend", (e) => {
            if (!selectionRect || !selectionRect.visible()) {
                // Always clear selection and transformer on click/tap on empty canvas
                if (e.target === stage) {
                    multiSelectedKonvaImgs = [];
                    window.selectedKonvaImg = null;

                    // Remove any transformer
                    let oldTransformer = layer.findOne(".table-transformer");
                    if (oldTransformer) {
                        oldTransformer.destroy();
                    }

                    // Clear input fields and disable
                    const customWidthInput = document.getElementById(
                        "form.custom_table_width"
                    );
                    const customHeightInput = document.getElementById(
                        "form.custom_table_height"
                    );
                    const customXInput = document.getElementById(
                        "form.custom_table_x"
                    );
                    const customYInput = document.getElementById(
                        "form.custom_table_y"
                    );

                    if (customWidthInput) {
                        customWidthInput.value = "";
                        customWidthInput.disabled = true;
                    }
                    if (customHeightInput) {
                        customHeightInput.value = "";
                        customHeightInput.disabled = true;
                    }
                    if (customXInput) {
                        customXInput.value = "";
                        customXInput.disabled = true;
                    }
                    if (customYInput) {
                        customYInput.value = "";
                        customYInput.disabled = true;
                    }

                    // Remove highlight from all
                    layer.getChildren().forEach((child) => {
                        child.strokeEnabled && child.strokeEnabled(false);
                        child.shadowEnabled && child.shadowEnabled(false);
                    });
                    layer.draw();
                }
                stage._isDragSelecting = false;
                return;
            }

            selectionRect.visible(false);

            // Find all images inside selection
            const rect = selectionRect.getClientRect();
            const selected = layer
                .getChildren()
                .filter(
                    (child) =>
                        child.className === "Image" &&
                        Konva.Util.haveIntersection(rect, child.getClientRect())
                );

            if (selected.length > 0) {
                // Always highlight all selected, and add transformer to first only
                highlightImage(selected);
            } else {
                // If nothing selected, clear selection and transformer
                multiSelectedKonvaImgs = [];
                window.selectedKonvaImg = null;
                // Remove highlight from all
                layer.getChildren().forEach((child) => {
                    child.strokeEnabled(false);
                    child.shadowEnabled(false);
                });
                // Remove any transformer
                let oldTransformer = layer.findOne(".table-transformer");
                if (oldTransformer) {
                    oldTransformer.destroy();
                }
            }
            layer.draw();
            stage._isDragSelecting = false;
        });

        // Remove previous event listener if any
        if (container._placeTableListener) {
            window.removeEventListener(
                "place-table",
                container._placeTableListener
            );
        }

        // Attach a new event listener that always uses the latest layer
        container._placeTableListener = function () {
            // Get selected club table value from dropdown
            const selectedOption = document.querySelector(
                '.fi-select-input-option[aria-selected="true"]'
            );
            let selectedValue = null;
            if (selectedOption) {
                selectedValue = selectedOption.getAttribute("data-value");
                const label = selectedOption.textContent.trim();
                // ...existing code...
            } else {
                // ...existing code...
            }

            // Get full club tables data from hidden field
            const clubTablesJson = document.querySelector(
                'input[name="club_tables_json"]'
            )?.value;
            let clubTables = [];
            if (clubTablesJson) {
                try {
                    clubTables = JSON.parse(clubTablesJson);
                } catch (e) {
                    // ...existing code...
                }
            }

            // Find the selected club table data
            let selectedClubTable = null;
            if (selectedValue && clubTables.length) {
                selectedClubTable = clubTables.find(
                    (table) => String(table.id) === String(selectedValue)
                );
            }

            // Get number of tables from input
            const numTablesInput = document.getElementById(
                "form.number_of_tables"
            );
            let numTables = 1;
            if (numTablesInput && numTablesInput.value) {
                numTables = parseInt(numTablesInput.value) || 1;
            }

            // Place images on the canvas using shape_url_full
            if (selectedClubTable && selectedClubTable.shape_url_full) {
                const imageUrl = selectedClubTable.shape_url_full;
                console.log(
                    "[Konva] Placing",
                    numTables,
                    "images with src:",
                    imageUrl
                );
                const width = container.offsetWidth;
                const height = container.offsetHeight;

                // Get custom width and height from Filament-styled input fields
                const customWidthInput = document.getElementById(
                    "form.custom_table_width"
                );
                const customHeightInput = document.getElementById(
                    "form.custom_table_height"
                );
                const customXInput = document.getElementById(
                    "form.custom_table_x"
                );
                const customYInput = document.getElementById(
                    "form.custom_table_y"
                );

                // Disable/clear X/Y fields initially
                if (customXInput) {
                    customXInput.disabled = true;
                    customXInput.value = "";
                }
                if (customYInput) {
                    customYInput.disabled = true;
                    customYInput.value = "";
                }

                // Disable fields initially
                if (customWidthInput) customWidthInput.disabled = true;
                if (customHeightInput) customHeightInput.disabled = true;

                // Always use default size for new tables
                let defaultWidth = 42;
                let defaultHeight = null;

                // Track selected image globally
                window.selectedKonvaImg = null;

                // Remove previous listeners to avoid duplicates
                if (customWidthInput) {
                    customWidthInput.oninput = null;
                }
                if (customHeightInput) {
                    customHeightInput.oninput = null;
                }

                for (let i = 0; i < numTables; i++) {
                    const imgObj = new window.Image();
                    imgObj.crossOrigin = "Anonymous";
                    imgObj.onload = function () {
                        let targetWidth = defaultWidth;
                        let targetHeight;
                        const aspectRatio = imgObj.width / imgObj.height;
                        if (defaultHeight) {
                            targetHeight = defaultHeight;
                        } else {
                            targetHeight = targetWidth / aspectRatio;
                        }
                        const konvaImg = new Konva.Image({
                            image: imgObj,
                            x: width / 2 - targetWidth / 2,
                            y: height / 2 - targetHeight / 2,
                            width: targetWidth,
                            height: targetHeight,
                            draggable: true,
                        });
                        // Click to select
                        konvaImg.on("click tap", function () {
                            highlightImage(konvaImg);
                            // Update fields with current size
                            if (customWidthInput)
                                customWidthInput.value = Math.round(
                                    konvaImg.width()
                                );
                            if (customHeightInput)
                                customHeightInput.value = Math.round(
                                    konvaImg.height()
                                );
                            if (customXInput)
                                customXInput.value = Math.round(konvaImg.x());
                            if (customYInput)
                                customYInput.value = Math.round(konvaImg.y());
                        });
                        // Group drag logic for multi-selected tables
                        let dragStartPositions = null;
                        konvaImg.on("dragstart", function (e) {
                            if (multiSelectedKonvaImgs.includes(konvaImg)) {
                                dragStartPositions = multiSelectedKonvaImgs.map(
                                    (img) => ({
                                        img,
                                        x: img.x(),
                                        y: img.y(),
                                    })
                                );
                            } else {
                                dragStartPositions = null;
                            }
                        });
                        konvaImg.on("dragmove", function (e) {
                            if (
                                multiSelectedKonvaImgs.length > 1 &&
                                multiSelectedKonvaImgs.includes(konvaImg) &&
                                dragStartPositions
                            ) {
                                const draggedImg = konvaImg;
                                const orig = dragStartPositions.find(
                                    (d) => d.img === draggedImg
                                );
                                const dx = draggedImg.x() - orig.x;
                                const dy = draggedImg.y() - orig.y;
                                multiSelectedKonvaImgs.forEach((selImg) => {
                                    if (selImg !== draggedImg) {
                                        const origSel = dragStartPositions.find(
                                            (d) => d.img === selImg
                                        );
                                        selImg.x(origSel.x + dx);
                                        selImg.y(origSel.y + dy);
                                    }
                                });
                                layer.batchDraw();
                                if (customXInput)
                                    customXInput.value = Math.round(
                                        multiSelectedKonvaImgs[0].x()
                                    );
                                if (customYInput)
                                    customYInput.value = Math.round(
                                        multiSelectedKonvaImgs[0].y()
                                    );
                            } else if (window.selectedKonvaImg === konvaImg) {
                                if (customXInput)
                                    customXInput.value = Math.round(
                                        konvaImg.x()
                                    );
                                if (customYInput)
                                    customYInput.value = Math.round(
                                        konvaImg.y()
                                    );
                            }
                        });
                        konvaImg.on("dragend", function (e) {
                            dragStartPositions = null;
                        });
                        layer.add(konvaImg);
                        layer.draw();
                    };

                    imgObj.onerror = function () {
                        console.error(
                            "[Konva] Failed to load image:",
                            imageUrl
                        );
                    };
                    imgObj.src = imageUrl;
                }

                // Listen for changes to custom width/height fields to update selected image
                function updateSelectedImageSize() {
                    if (
                        multiSelectedKonvaImgs &&
                        multiSelectedKonvaImgs.length > 0
                    ) {
                        let w =
                            customWidthInput && customWidthInput.value
                                ? parseInt(customWidthInput.value)
                                : null;
                        let h =
                            customHeightInput && customHeightInput.value
                                ? parseInt(customHeightInput.value)
                                : null;
                        multiSelectedKonvaImgs.forEach((img) => {
                            if (w !== null && !isNaN(w)) img.width(w);
                            if (h !== null && !isNaN(h)) img.height(h);
                        });
                        layer.draw();
                        // After update, re-run highlight to update input values (in case of mixed values)
                        highlightImage(multiSelectedKonvaImgs);
                    }
                }

                if (customWidthInput) {
                    customWidthInput.addEventListener(
                        "input",
                        updateSelectedImageSize
                    );
                }
                if (customHeightInput) {
                    customHeightInput.addEventListener(
                        "input",
                        updateSelectedImageSize
                    );
                }

                // Listen for changes to custom X/Y fields to update selected image position
                function updateSelectedImagePosition() {
                    if (
                        multiSelectedKonvaImgs &&
                        multiSelectedKonvaImgs.length > 0
                    ) {
                        let x =
                            customXInput && customXInput.value
                                ? parseInt(customXInput.value)
                                : null;
                        let y =
                            customYInput && customYInput.value
                                ? parseInt(customYInput.value)
                                : null;
                        multiSelectedKonvaImgs.forEach((img) => {
                            if (x !== null && !isNaN(x)) img.x(x);
                            if (y !== null && !isNaN(y)) img.y(y);
                        });
                        layer.draw();
                        // After update, re-run highlight to update input values (in case of mixed values)
                        highlightImage(multiSelectedKonvaImgs);
                    }
                }

                if (customXInput) {
                    customXInput.addEventListener(
                        "input",
                        updateSelectedImagePosition
                    );
                }
                if (customYInput) {
                    customYInput.addEventListener(
                        "input",
                        updateSelectedImagePosition
                    );
                }
            } else {
                console.log(
                    "[Konva] shape_url_full not found for selected Club Table."
                );
            }
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
        // Log canvas size on every mutation
        const canvasDiv = document.getElementById(containerId);
        if (canvasDiv) {
            // If canvas is missing its <canvas> child, re-initializing
            if (!canvasDiv.querySelector("canvas")) {
                window.initSeatmapKonva(containerId);
            }
        }
    });
    observer.observe(container.parentNode, { childList: true, subtree: true });
    container._konvaMutationObserver = observer;
};
