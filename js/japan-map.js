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

      // Store original viewBox
      this.originalViewBox = this.svgElement.getAttribute("viewBox");

      // Style SVG
      this.svgElement.style.width = "100%";
      this.svgElement.style.height = "100%";
      this.svgElement.style.display = "block";

      // Setup prefecture elements
      this.setupPrefectures();
      this.applyColors();
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

      // Add region labels
      this.addRegionLabels();
    }

    addRegionLabels() {
      // Remove existing labels if any
      const existingLabels = this.container.querySelectorAll(".region-label");
      existingLabels.forEach((label) => label.remove());

      const existingContainer = this.container.querySelector(
        ".region-labels-container",
      );
      if (existingContainer) {
        existingContainer.remove();
      }

      if (this.selectedRegion) {
        // Don't show region labels when zoomed in
        return;
      }

      // Create label container
      const labelContainer = document.createElement("div");
      labelContainer.className = "region-labels-container";
      labelContainer.style.position = "absolute";
      labelContainer.style.top = "0";
      labelContainer.style.left = "0";
      labelContainer.style.width = "100%";
      labelContainer.style.height = "100%";
      labelContainer.style.pointerEvents = "none";
      labelContainer.style.zIndex = "10";

      this.regions.forEach((region) => {
        const position = this.getRegionLabelPosition(region);
        if (!position) return;

        const label = document.createElement("div");
        label.className = "region-label";
        label.textContent = region.name;
        label.style.position = "absolute";
        label.style.left = `${position.x}%`;
        label.style.top = `${position.y}%`;
        label.style.transform = "translate(-50%, -50%)";
        label.style.color = "#fff";
        label.style.fontWeight = "900";
        label.style.fontSize = "clamp(14px, 2vw, 20px)";
        label.style.textShadow =
          "0 2px 8px rgba(0,0,0,0.3), 0 0 2px rgba(0,0,0,0.5)";
        label.style.letterSpacing = "0.05em";
        label.style.pointerEvents = "none";
        label.style.userSelect = "none";
        label.style.opacity = "0";
        label.style.animation = "labelFadeIn 0.6s ease-out forwards";
        label.style.animationDelay = `${region.labelDelay || 0}ms`;

        labelContainer.appendChild(label);
      });

      this.container.appendChild(labelContainer);
    }

    getRegionLabelPosition(region) {
      // Calculate center position of region based on its prefectures
      let minX = Infinity,
        minY = Infinity,
        maxX = -Infinity,
        maxY = -Infinity;
      let found = false;

      region.prefectures.forEach((pref) => {
        const el = this.svgElement.querySelector(`.${pref.id}`);
        if (el && el.getBBox) {
          const bbox = el.getBBox();
          minX = Math.min(minX, bbox.x);
          minY = Math.min(minY, bbox.y);
          maxX = Math.max(maxX, bbox.x + bbox.width);
          maxY = Math.max(maxY, bbox.y + bbox.height);
          found = true;
        }
      });

      if (!found) return null;

      // Get SVG viewBox
      const viewBox = this.svgElement.viewBox.baseVal;
      const svgWidth = viewBox.width;
      const svgHeight = viewBox.height;
      const svgX = viewBox.x;
      const svgY = viewBox.y;

      // Calculate center
      const centerX = (minX + maxX) / 2;
      const centerY = (minY + maxY) / 2;

      // Convert to percentage
      const percentX = ((centerX - svgX) / svgWidth) * 100;
      const percentY = ((centerY - svgY) / svgHeight) * 100;

      return { x: percentX, y: percentY };
    }

    handlePrefectureClick(el) {
      // Extract prefecture ID from class
      const classList = Array.from(el.classList);
      const prefId = classList.find(
        (c) =>
          c !== "prefecture" &&
          c !== "geolonia-svg-map-prefecture" &&
          !REGION_IDS.includes(c),
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

        region.prefectures.forEach((pref) => {
          const el = Array.from(prefElements).find((e) =>
            e.classList.contains(pref.id),
          );

          if (el) {
            if (this.selectedRegion) {
              // Region selected mode
              if (isRegionSelected) {
                el.style.fill = REGION_COLORS.selected;
                el.style.pointerEvents = "auto";
              } else {
                el.style.fill = "#f1f5f9"; // slate-100 (inactive)
                el.style.pointerEvents = "none";
              }
            } else {
              // Overview mode - color by region
              el.style.fill = REGION_COLORS[region.id] || "#cbd5e1";
              el.style.pointerEvents = "auto";
            }
          }
        });
      });

      // Update labels
      this.addRegionLabels();
    }

    zoomToRegion(region) {
      let minX = Infinity,
        minY = Infinity,
        maxX = -Infinity,
        maxY = -Infinity;
      let found = false;

      region.prefectures.forEach((pref) => {
        const el = this.svgElement.querySelector(`.${pref.id}`);
        if (el && el.getBBox) {
          const bbox = el.getBBox();
          minX = Math.min(minX, bbox.x);
          minY = Math.min(minY, bbox.y);
          maxX = Math.max(maxX, bbox.x + bbox.width);
          maxY = Math.max(maxY, bbox.y + bbox.height);
          found = true;
        }
      });

      if (found) {
        const padding = 20;
        const viewBox = `${minX - padding} ${minY - padding} ${maxX - minX + padding * 2} ${maxY - minY + padding * 2}`;
        this.svgElement.setAttribute("viewBox", viewBox);
      }
    }

    resetZoom() {
      if (this.originalViewBox) {
        this.svgElement.setAttribute("viewBox", this.originalViewBox);
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
