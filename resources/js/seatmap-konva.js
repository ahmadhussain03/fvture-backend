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
            // Get selected club table value from dropdown
            const selectedOption = document.querySelector(
                '.fi-select-input-option[aria-selected="true"]'
            );
            let selectedValue = null;
            if (selectedOption) {
                selectedValue = selectedOption.getAttribute("data-value");
                const label = selectedOption.textContent.trim();
                console.log("[Konva] Selected Club Table:", {
                    label,
                    value: selectedValue,
                });
            } else {
                console.log("[Konva] No Club Table selected");
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
                    console.error("[Konva] Error parsing club_tables_json:", e);
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
                let customWidth =
                    customWidthInput && customWidthInput.value
                        ? parseInt(customWidthInput.value)
                        : 42;
                let customHeight =
                    customHeightInput && customHeightInput.value
                        ? parseInt(customHeightInput.value)
                        : null;
                // Track selected image globally in this closure
                let selectedKonvaImg = null;
                // Helper to update highlight
                function highlightImage(img) {
                    layer.getChildren().forEach((child) => {
                        child.strokeEnabled(false);
                        child.shadowEnabled(false);
                    });
                    img.stroke("orange");
                    img.strokeWidth(3);
                    img.strokeEnabled(true);
                    img.shadowColor("orange");
                    img.shadowBlur(10);
                    img.shadowEnabled(true);
                    layer.draw();
                }

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
                        let targetWidth = customWidth;
                        let targetHeight;
                        const aspectRatio = imgObj.width / imgObj.height;
                        if (customHeight) {
                            targetHeight = customHeight;
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
                            selectedKonvaImg = konvaImg;
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
                        });
                        layer.add(konvaImg);
                        layer.draw();
                        console.log("[Konva] Image added to layer:", konvaImg);
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
                    if (selectedKonvaImg) {
                        let w =
                            customWidthInput && customWidthInput.value
                                ? parseInt(customWidthInput.value)
                                : selectedKonvaImg.width();
                        let h =
                            customHeightInput && customHeightInput.value
                                ? parseInt(customHeightInput.value)
                                : selectedKonvaImg.height();
                        selectedKonvaImg.width(w);
                        selectedKonvaImg.height(h);
                        layer.draw();
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
