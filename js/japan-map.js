/**
 * Japan Map Component
 * SVG-based interactive map of Japan with region/prefecture selection
 */

(function () {
  "use strict";

  const REGION_COLORS = {
    hokkaido: "#3b82f6", // blue-500
    tohoku: "#06b6d4", // cyan-500
    kanto: "#8b5cf6", // violet-500
    chubu: "#10b981", // emerald-500
    kansai: "#f59e0b", // amber-500
    chugoku: "#f97316", // orange-500
    shikoku: "#ef4444", // red-500
    kyushu: "#ec4899", // pink-500
    selected: "#4f46e5", // indigo-600
  };

  const REGION_IDS = [
    "hokkaido",
    "tohoku",
    "kanto",
    "chubu",
    "kansai",
    "chugoku",
    "shikoku",
    "kyushu",
  ];

  class JapanMap {
    constructor(containerId) {
      this.container = document.getElementById(containerId);
      if (!this.container) {
        console.error(`Container ${containerId} not found`);
        return;
      }

      this.svgContent = null;
      this.svgElement = null;
      this.selectedRegion = null;
      this.regions = [];
      this.originalViewBox = null;

      this.init();
    }

    async init() {
      // Load regions data
      this.regions = this.getRegionsData();

      // Load SVG
      try {
        const response = await fetch(
          "/wp-content/themes/yoruo-navi/assets/map-mobile.svg",
        );
        this.svgContent = await response.text();
        this.render();
      } catch (error) {
        console.error("Failed to load map SVG:", error);
      }
    }

    render() {
      if (!this.svgContent) return;

      // Insert SVG into container
      this.container.innerHTML = this.svgContent;
      this.svgElement = this.container.querySelector("svg");

      if (!this.svgElement) return;

      // Style SVG
      this.svgElement.style.width = "100%";
      // this.svgElement.style.height = "100%"; // Removed to allow natural height based on aspect ratio
      this.svgElement.style.display = "block";
      // Force center alignment
      this.svgElement.setAttribute("preserveAspectRatio", "xMidYMid meet");

      // Store original viewBox initially from attribute
      this.originalViewBox = this.svgElement.getAttribute("viewBox");

      // Setup prefecture elements
      this.setupPrefectures();
      this.applyColors();

      // Adjust viewBox to fit content tightly
      // Use polling to handle visibility
      let retries = 0;
      const tryFit = () => {
        if (this.fitMapToContainer()) {
          // Success
        } else if (retries < 20) {
          retries++;
          setTimeout(tryFit, 100);
        }
      };
      // Start trying
      setTimeout(tryFit, 0);
    }

    fitMapToContainer() {
      const svg = this.svgElement;
      if (!svg) return false;

      // Ensure SVG is in DOM and likely rendered
      if (!svg.isConnected || svg.clientWidth === 0) return false;

      let minX = Infinity,
        minY = Infinity,
        maxX = -Infinity,
        maxY = -Infinity;
      let found = false;

      const prefectures = svg.querySelectorAll(".prefecture");
      if (prefectures.length === 0) return true;

      try {
        prefectures.forEach((el) => {
          const bbox = el.getBBox();
          if (bbox.width <= 0 || bbox.height <= 0) return;

          // Use getScreenCTM for robust coordinate transformation to SVG root space
          // This handles nested transforms and existing viewBox correctly
          const corners = [
            { x: bbox.x, y: bbox.y },
            { x: bbox.x + bbox.width, y: bbox.y },
            { x: bbox.x + bbox.width, y: bbox.y + bbox.height },
            { x: bbox.x, y: bbox.y + bbox.height },
          ];

          corners.forEach((p) => {
            const pt = svg.createSVGPoint();
            pt.x = p.x;
            pt.y = p.y;

            let transPt = pt;
            try {
              const screenCTM = el.getScreenCTM();
              const rootScreenCTM = svg.getScreenCTM();

              if (screenCTM && rootScreenCTM) {
                const globalMatrix = rootScreenCTM
                  .inverse()
                  .multiply(screenCTM);
                transPt = pt.matrixTransform(globalMatrix);
              } else {
                // Fallback to getCTM if screen CTMs fail (e.g. not attached)
                // though the retry loop should prevent this
                const matrix = el.getCTM();
                if (matrix) transPt = pt.matrixTransform(matrix);
              }
            } catch (e) {
              // Ignore calculation errors
            }

            minX = Math.min(minX, transPt.x);
            minY = Math.min(minY, transPt.y);
            maxX = Math.max(maxX, transPt.x);
            maxY = Math.max(maxY, transPt.y);
          });
          found = true;
        });
      } catch (e) {
        return false;
      }

      if (found && minX !== Infinity) {
        // Add moderate padding (5%)
        const width = maxX - minX;
        const height = maxY - minY;
        const paddingX = width * 0.05;
        const paddingY = height * 0.05;

        const newViewBox = `${minX - paddingX} ${minY - paddingY} ${width + paddingX * 2} ${height + paddingY * 2}`;
        svg.setAttribute("viewBox", newViewBox);

        // Dynamically set aspect-ratio to remove extra whitespace (Only if NOT fixed height mode)
        if (this.container.dataset.fixedHeight !== "true") {
          const aspectRatio = (width + paddingX * 2) / (height + paddingY * 2);
          this.container.style.aspectRatio = aspectRatio.toString();
          // Ensure container doesn't force height
          this.container.style.height = "auto";
          this.svgElement.style.height = "100%"; // Changed from commented out to 100% to fill container
        } else {
          // Fixed height mode (e.g. mini map in modal)
          this.container.style.aspectRatio = "";
          this.container.style.height = "100%";
          this.svgElement.style.height = "100%";
        }

        this.originalViewBox = newViewBox;
        return true; // Success
      }

      return false; // Not ready
    }

    setupPrefectures() {
      const prefElements = this.svgElement.querySelectorAll(".prefecture");

      prefElements.forEach((el) => {
        // Base styles
        el.style.fill = "#e2e8f0"; // slate-200
        el.style.stroke = "#fff";
        el.style.strokeWidth = "1";
        el.style.cursor = "pointer";
        el.style.transition = "fill 0.3s ease";

        // Click handler
        el.addEventListener("click", (e) => {
          e.stopPropagation();
          this.handlePrefectureClick(el);
        });

        // Hover effect
        el.addEventListener("mouseenter", () => {
          if (!this.selectedRegion) {
            el.style.opacity = "0.8";
          }
        });

        el.addEventListener("mouseleave", () => {
          el.style.opacity = "1";
        });
      });

      this.addRegionLabels();
    }

    addRegionLabels() {
      // Remove existing SVG labels if any
      const existingLabels =
        this.svgElement.querySelectorAll(".region-label-text");
      existingLabels.forEach((label) => label.remove());

      if (this.selectedRegion) {
        // Don't show region labels when zoomed in
        return;
      }

      // Find the group containing prefectures to append labels to
      const prefecturesGroup = this.svgElement.querySelector(".prefectures");
      if (!prefecturesGroup) return;

      this.regions.forEach((region, index) => {
        const position = this.getRegionCenter(region, prefecturesGroup);
        if (!position) return;

        // Create SVG text element
        const text = document.createElementNS(
          "http://www.w3.org/2000/svg",
          "text",
        );
        text.setAttribute("x", position.x);
        text.setAttribute("y", position.y);
        text.setAttribute("class", "region-label-text");
        text.textContent = region.name;

        // Adjust font size
        text.setAttribute("font-size", "28px");

        text.style.animationDelay = `${index * 50}ms`;

        // Append to the same group as prefectures
        prefecturesGroup.appendChild(text);
      });
    }

    addPrefectureLabels(region) {
      // Remove existing pref labels
      const existingLabels =
        this.svgElement.querySelectorAll(".pref-label-text");
      existingLabels.forEach((label) => label.remove());

      if (!region) return;

      const prefecturesGroup = this.svgElement.querySelector(".prefectures");
      if (!prefecturesGroup) return;

      region.prefectures.forEach((pref, index) => {
        const el = this.svgElement.querySelector(`.${pref.id}`);
        if (!el) return;

        const position = this.getPrefectureCenter(el, prefecturesGroup);
        if (!position) return;

        // Create SVG text element
        const text = document.createElementNS(
          "http://www.w3.org/2000/svg",
          "text",
        );
        text.setAttribute("x", position.x);
        text.setAttribute("y", position.y);
        text.setAttribute("class", "pref-label-text");
        text.textContent = pref.name;

        // Smaller font size for individual prefectures
        // Since we are zoomed in, this will appear readable
        // We might need to scale it inversely to the zoom level if we want constant size
        // But simple fixed size often works well with SVG scaling (it gets bigger as we zoom)
        // Let's try a small base size.
        text.setAttribute("font-size", "10px");

        text.style.animationDelay = `${index * 30}ms`;

        prefecturesGroup.appendChild(text);
      });
    }

    getPrefectureCenter(el, containerGroup) {
      if (!el) return null;

      const rect = el.getBoundingClientRect();
      if (rect.width === 0 || rect.height === 0) return null;

      const screenCenterX = rect.left + rect.width / 2;
      const screenCenterY = rect.top + rect.height / 2;

      const pt = this.svgElement.createSVGPoint();
      pt.x = screenCenterX;
      pt.y = screenCenterY;

      try {
        const globalToLocalMatrix = containerGroup.getScreenCTM().inverse();
        const localPoint = pt.matrixTransform(globalToLocalMatrix);
        return { x: localPoint.x, y: localPoint.y };
      } catch (e) {
        return null; // Fallback
      }
    }

    getRegionCenter(region, containerGroup) {
      // 1. Calculate the bounding box of the region in SCREEN coordinates
      let minX = Infinity,
        minY = Infinity,
        maxX = -Infinity,
        maxY = -Infinity;
      let found = false;

      region.prefectures.forEach((pref) => {
        const el = this.svgElement.querySelector(`.${pref.id}`);
        if (el) {
          const rect = el.getBoundingClientRect();
          if (rect.width > 0 && rect.height > 0) {
            minX = Math.min(minX, rect.left);
            minY = Math.min(minY, rect.top);
            maxX = Math.max(maxX, rect.right);
            maxY = Math.max(maxY, rect.bottom);
            found = true;
          }
        }
      });

      if (!found) return null;

      // 2. Calculate the center point in SCREEN coordinates
      const screenCenterX = (minX + maxX) / 2;
      const screenCenterY = (minY + maxY) / 2;

      // 3. Convert SCREEN coordinates to SVG LOCAL coordinates
      const pt = this.svgElement.createSVGPoint();
      pt.x = screenCenterX;
      pt.y = screenCenterY;

      try {
        const globalToLocalMatrix = containerGroup.getScreenCTM().inverse();
        const localPoint = pt.matrixTransform(globalToLocalMatrix);
        return { x: localPoint.x, y: localPoint.y };
      } catch (e) {
        return null;
      }
    }

    handlePrefectureClick(el) {
      // ... (unchanged)
      // Extract prefecture ID from class
      const classList = Array.from(el.classList);
      const prefId = classList.find(
        (c) =>
          c !== "prefecture" &&
          c !== "geolonia-svg-map-prefecture" &&
          (!REGION_IDS.includes(c) || c === "hokkaido"),
      );

      if (!prefId) return;

      // Find which region this prefecture belongs to
      const region = this.regions.find((r) =>
        r.prefectures.some((p) => p.id === prefId),
      );

      if (!region) return;

      if (!this.selectedRegion) {
        // Region selection mode
        this.selectRegion(region);
      } else {
        // Prefecture selection mode
        if (this.selectedRegion.id === region.id) {
          const pref = region.prefectures.find((p) => p.id === prefId);
          if (pref) {
            this.selectPrefecture(pref);
          }
        }
      }
    }

    selectRegion(region) {
      this.selectedRegion = region;
      this.applyColors();
      this.zoomToRegion(region);

      // Trigger custom event
      const event = new CustomEvent("regionSelected", { detail: region });
      this.container.dispatchEvent(event);
    }

    selectPrefecture(pref) {
      // Trigger custom event
      const event = new CustomEvent("prefectureSelected", { detail: pref });
      this.container.dispatchEvent(event);
    }

    resetSelection() {
      this.selectedRegion = null;
      this.applyColors();
      this.resetZoom();
    }

    applyColors() {
      const prefElements = this.svgElement.querySelectorAll(".prefecture");

      this.regions.forEach((region) => {
        const isRegionSelected =
          this.selectedRegion && this.selectedRegion.id === region.id;

        // Pre-generate color variants for this region if selected
        let colorVariants = [];
        if (isRegionSelected) {
          const baseColor = REGION_COLORS[region.id];
          colorVariants = this.generateColorVariants(
            baseColor,
            region.prefectures.length,
          );
        }

        region.prefectures.forEach((pref, index) => {
          const el = Array.from(prefElements).find((e) =>
            e.classList.contains(pref.id),
          );

          if (el) {
            if (this.selectedRegion) {
              // Region selected mode
              if (isRegionSelected) {
                // Assign variant color based on index
                // Use modulus to cycle through variants if fewer variants than prefectures
                el.style.fill = colorVariants[index % colorVariants.length];
                el.style.pointerEvents = "auto";

                // Keep stroke white but maybe slightly thicker
                el.style.stroke = "#fff";
                el.style.strokeWidth = "1.5";
              } else {
                el.style.fill = "#f1f5f9"; // slate-100 (inactive)
                el.style.pointerEvents = "none";
              }
            } else {
              // Overview mode - color by region
              el.style.fill = REGION_COLORS[region.id] || "#cbd5e1";
              el.style.pointerEvents = "auto";
              el.style.strokeWidth = "1";
            }
          }
        });
      });

      // Update labels
      this.addRegionLabels();
      this.addPrefectureLabels(this.selectedRegion);
    }

    generateColorVariants(baseColor, count) {
      // Simple logic to generate lighter/darker shades of a hex color
      // baseColor is expected to be hex string like "#3b82f6"

      const variants = [];

      // Helper to adjust brightness
      // p: positive for lighter, negative for darker
      const adjustBrightness = (hex, p) => {
        // Strip hash
        hex = hex.replace("#", "");

        let r = parseInt(hex.substring(0, 2), 16);
        let g = parseInt(hex.substring(2, 4), 16);
        let b = parseInt(hex.substring(4, 6), 16);

        // Calculate new colors
        // If p > 0, we mix with white (255)
        // If p < 0, we mix with black (0)

        if (p > 0) {
          // Lighten
          r = Math.round(r + (255 - r) * p);
          g = Math.round(g + (255 - g) * p);
          b = Math.round(b + (255 - b) * p);
        } else {
          // Darken
          r = Math.round(r * (1 + p));
          g = Math.round(g * (1 + p));
          b = Math.round(b * (1 + p));
        }

        const toHex = (c) => {
          const hex = c.toString(16);
          return hex.length === 1 ? "0" + hex : hex;
        };

        return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
      };

      // Generate a range of variations
      // We want distinct enough colors
      variants.push(baseColor); // Original
      variants.push(adjustBrightness(baseColor, 0.2)); // Lighter
      variants.push(adjustBrightness(baseColor, -0.1)); // Slightly darker
      variants.push(adjustBrightness(baseColor, 0.4)); // Very light
      variants.push(adjustBrightness(baseColor, -0.2)); // Darker
      variants.push(adjustBrightness(baseColor, 0.1)); // Very slightly lighter

      return variants;
    }

    zoomToRegion(region) {
      let minX = Infinity,
        minY = Infinity,
        maxX = -Infinity,
        maxY = -Infinity;
      let found = false;

      // Get the transformation matrix from the element to the SVG root
      // This is crucial because the map group has a transform applied
      const svgRoot = this.svgElement;

      region.prefectures.forEach((pref) => {
        const el = this.svgElement.querySelector(`.${pref.id}`);
        if (el && el.getBBox) {
          const bbox = el.getBBox();

          // Helper to transform a point
          const transformPoint = (x, y) => {
            const pt = svgRoot.createSVGPoint();
            pt.x = x;
            pt.y = y;
            // Get transformation matrix from element to SVG root
            try {
              const matrix = el.getCTM();
              // Since getCTM returns mapping to viewport (screen), we might need logic relative to SVG root
              // But viewBox is in root user coordinate system.
              // Usually el.getCTM() maps to client coordinates if processed naively,
              // but we want coordinates in the SVG's root user space.
              // Actually, getCTM() returns the matrix transforming the element's coordinate system
              // to the SVG viewport's coordinate system.
              // If the SVG has no viewBox, viewport == user space.
              // If SVG has viewBox, we need to map via screenCTM inverse?

              // Let's use more robust approach:
              // We need coordinates in the SVG root system (where viewBox is defined).
              // el.getScreenCTM() -> transforms local to screen
              // svgRoot.getScreenCTM().inverse() -> transforms screen to svg root

              const screenCTM = el.getScreenCTM();
              const rootScreenCTM = svgRoot.getScreenCTM();

              if (screenCTM && rootScreenCTM) {
                const globalMatrix = rootScreenCTM
                  .inverse()
                  .multiply(screenCTM);
                return pt.matrixTransform(globalMatrix);
              }
              return pt; // Fallback
            } catch (e) {
              return pt;
            }
          };

          // Transform all 4 corners of bbox to account for rotation/scale
          const corners = [
            transformPoint(bbox.x, bbox.y),
            transformPoint(bbox.x + bbox.width, bbox.y),
            transformPoint(bbox.x, bbox.y + bbox.height),
            transformPoint(bbox.x + bbox.width, bbox.y + bbox.height),
          ];

          corners.forEach((p) => {
            minX = Math.min(minX, p.x);
            minY = Math.min(minY, p.y);
            maxX = Math.max(maxX, p.x);
            maxY = Math.max(maxY, p.y);
          });

          found = true;
        }
      });

      if (found) {
        // Add padding
        const width = maxX - minX;
        const height = maxY - minY;
        const paddingX = width * 0.1; // 10% padding
        const paddingY = height * 0.1;

        const viewBox = `${minX - paddingX} ${minY - paddingY} ${width + paddingX * 2} ${height + paddingY * 2}`;

        // Animate viewBox change (simple implementation)
        this.svgElement.style.transition = "all 0.5s ease";
        this.svgElement.setAttribute("viewBox", viewBox);

        // Hide regional labels immediately
        const labels = this.svgElement.querySelectorAll(".region-label-text");
        labels.forEach((l) => (l.style.opacity = "0"));
      }
    }

    zoomToPrefecture(prefId) {
      const el = this.svgElement.querySelector(`.${prefId}`);
      if (!el) return;

      // Helper to transform a point (reused logic could be refactored but inline for safety)
      const transformPoint = (x, y) => {
        const pt = this.svgElement.createSVGPoint();
        pt.x = x;
        pt.y = y;
        try {
          const screenCTM = el.getScreenCTM();
          const rootScreenCTM = this.svgElement.getScreenCTM();
          if (screenCTM && rootScreenCTM) {
            const globalMatrix = rootScreenCTM.inverse().multiply(screenCTM);
            return pt.matrixTransform(globalMatrix);
          }
          return pt;
        } catch (e) {
          return pt;
        }
      };

      try {
        const bbox = el.getBBox();
        const corners = [
          transformPoint(bbox.x, bbox.y),
          transformPoint(bbox.x + bbox.width, bbox.y),
          transformPoint(bbox.x, bbox.y + bbox.height),
          transformPoint(bbox.x + bbox.width, bbox.y + bbox.height),
        ];

        let minX = Infinity,
          minY = Infinity,
          maxX = -Infinity,
          maxY = -Infinity;
        corners.forEach((p) => {
          minX = Math.min(minX, p.x);
          minY = Math.min(minY, p.y);
          maxX = Math.max(maxX, p.x);
          maxY = Math.max(maxY, p.y);
        });

        const width = maxX - minX;
        const height = maxY - minY;
        // Larger padding for single prefecture to give context
        const padding = Math.max(width, height) * 0.5;

        // Ensure aspect ratio of container is maintained if needed, but here we just set viewBox
        // The container CSS handles aspect ratio issues (we removed fixed height earlier)

        const newViewBox = `${minX - padding / 2} ${minY - padding / 2} ${width + padding} ${height + padding}`;

        this.svgElement.style.transition =
          "all 0.8s cubic-bezier(0.22, 1, 0.36, 1)";
        this.svgElement.setAttribute("viewBox", newViewBox);

        // Hide labels
        const labels = this.svgElement.querySelectorAll(
          ".region-label-text, .pref-label-text",
        );
        labels.forEach((l) => (l.style.opacity = "0"));
      } catch (e) {
        console.error("Zoom failed", e);
      }
    }

    resetZoom() {
      if (this.originalViewBox) {
        this.svgElement.setAttribute("viewBox", this.originalViewBox);
        // Reset labels
        const labels = this.svgElement.querySelectorAll(".region-label-text");
        labels.forEach((l) => (l.style.opacity = "1"));
      }
    }

    getRegionsData() {
      // Return regions with their prefectures
      return [
        {
          id: "hokkaido",
          name: "北海道",
          prefectures: [{ id: "hokkaido", name: "北海道", code: "01" }],
        },
        {
          id: "tohoku",
          name: "東北",
          prefectures: [
            { id: "aomori", name: "青森", code: "02" },
            { id: "iwate", name: "岩手", code: "03" },
            { id: "miyagi", name: "宮城", code: "04" },
            { id: "akita", name: "秋田", code: "05" },
            { id: "yamagata", name: "山形", code: "06" },
            { id: "fukushima", name: "福島", code: "07" },
          ],
        },
        {
          id: "kanto",
          name: "関東",
          prefectures: [
            { id: "ibaraki", name: "茨城", code: "08" },
            { id: "tochigi", name: "栃木", code: "09" },
            { id: "gunma", name: "群馬", code: "10" },
            { id: "saitama", name: "埼玉", code: "11" },
            { id: "chiba", name: "千葉", code: "12" },
            { id: "tokyo", name: "東京", code: "13" },
            { id: "kanagawa", name: "神奈川", code: "14" },
          ],
        },
        {
          id: "chubu",
          name: "中部",
          prefectures: [
            { id: "niigata", name: "新潟", code: "15" },
            { id: "toyama", name: "富山", code: "16" },
            { id: "ishikawa", name: "石川", code: "17" },
            { id: "fukui", name: "福井", code: "18" },
            { id: "yamanashi", name: "山梨", code: "19" },
            { id: "nagano", name: "長野", code: "20" },
            { id: "gifu", name: "岐阜", code: "21" },
            { id: "shizuoka", name: "静岡", code: "22" },
            { id: "aichi", name: "愛知", code: "23" },
          ],
        },
        {
          id: "kansai",
          name: "関西",
          prefectures: [
            { id: "mie", name: "三重", code: "24" },
            { id: "shiga", name: "滋賀", code: "25" },
            { id: "kyoto", name: "京都", code: "26" },
            { id: "osaka", name: "大阪", code: "27" },
            { id: "hyogo", name: "兵庫", code: "28" },
            { id: "nara", name: "奈良", code: "29" },
            { id: "wakayama", name: "和歌山", code: "30" },
          ],
        },
        {
          id: "chugoku",
          name: "中国",
          prefectures: [
            { id: "tottori", name: "鳥取", code: "31" },
            { id: "shimane", name: "島根", code: "32" },
            { id: "okayama", name: "岡山", code: "33" },
            { id: "hiroshima", name: "広島", code: "34" },
            { id: "yamaguchi", name: "山口", code: "35" },
          ],
        },
        {
          id: "shikoku",
          name: "四国",
          prefectures: [
            { id: "tokushima", name: "徳島", code: "36" },
            { id: "kagawa", name: "香川", code: "37" },
            { id: "ehime", name: "愛媛", code: "38" },
            { id: "kochi", name: "高知", code: "39" },
          ],
        },
        {
          id: "kyushu",
          name: "九州・沖縄",
          prefectures: [
            { id: "fukuoka", name: "福岡", code: "40" },
            { id: "saga", name: "佐賀", code: "41" },
            { id: "nagasaki", name: "長崎", code: "42" },
            { id: "kumamoto", name: "熊本", code: "43" },
            { id: "oita", name: "大分", code: "44" },
            { id: "miyazaki", name: "宮崎", code: "45" },
            { id: "kagoshima", name: "鹿児島", code: "46" },
            { id: "okinawa", name: "沖縄", code: "47" },
          ],
        },
      ];
    }
  }

  // Export to global scope
  window.JapanMap = JapanMap;
})();
