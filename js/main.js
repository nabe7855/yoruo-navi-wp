document.addEventListener("DOMContentLoaded", function () {
  // --- Slider Logic ---
  const sliderTrack = document.querySelector(".slider-track");
  const sliderItems = document.querySelectorAll(".slider-item");
  if (sliderTrack && sliderItems.length > 0) {
    let currentIndex = 0;
    const totalItems = sliderItems.length;
    const nextBtn = document.querySelector(".slider-next");
    const prevBtn = document.querySelector(".slider-prev");

    function updateSlider() {
      const slideWidth = 75;
      const offset = 50 - slideWidth / 2 - currentIndex * slideWidth;
      sliderTrack.style.transform = `translateX(${offset}%)`;

      sliderItems.forEach((item, index) => {
        if (index === currentIndex) {
          item.classList.add("z-20", "scale-100", "opacity-100");
          item.classList.remove("z-10", "scale-[0.88]", "opacity-60");
        } else {
          item.classList.remove("z-20", "scale-100", "opacity-100");
          item.classList.add("z-10", "scale-[0.88]", "opacity-60");
        }
      });
    }

    if (nextBtn)
      nextBtn.addEventListener("click", () => {
        currentIndex = (currentIndex + 1) % totalItems;
        updateSlider();
      });
    if (prevBtn)
      prevBtn.addEventListener("click", () => {
        currentIndex = (currentIndex - 1 + totalItems) % totalItems;
        updateSlider();
      });
    setInterval(() => {
      currentIndex = (currentIndex + 1) % totalItems;
      updateSlider();
    }, 5000);
    updateSlider();
  }

  // --- Search Accordion ---
  const accordionToggle = document.getElementById("search-accordion-toggle");
  const accordionContent = document.getElementById("search-accordion-content");
  const accordionArrow = document.getElementById("accordion-arrow");
  if (accordionToggle && accordionContent) {
    accordionToggle.addEventListener("click", () => {
      const isOpen = accordionContent.classList.contains("opacity-100");
      if (isOpen) {
        accordionContent.style.maxHeight = "0";
        accordionContent.classList.remove("opacity-100");
        accordionContent.classList.add("opacity-0");
        accordionArrow.style.transform = "rotate(0deg)";
        accordionToggle.classList.remove("bg-cyan-500", "text-white");
        accordionToggle.classList.add("bg-slate-100", "text-slate-600");
      } else {
        accordionContent.style.maxHeight = "1000px";
        accordionContent.classList.remove("opacity-0");
        accordionContent.classList.add("opacity-100");
        accordionArrow.style.transform = "rotate(90deg)";
        accordionToggle.classList.remove("bg-slate-100", "text-slate-600");
        accordionToggle.classList.add("bg-cyan-500", "text-white");
      }
    });
  }

  // --- Modal Logic & Form Syncing ---
  const modalContainer = document.getElementById("search-modal-container");
  const modalBody = document.getElementById("modal-body");
  const modalTitle = document.getElementById("modal-title");
  const mainForm = document.getElementById("main-search-form");
  const triggers = document.querySelectorAll(".search-modal-trigger");
  const closeBtns = document.querySelectorAll(".modal-close, .modal-overlay");

  const modalTitles = {
    map: "エリアで探す",
    "job-type": "職種で探す",
    salary: "給与で探す",
    "work-style": "働き方で探す",
  };

  triggers.forEach((trigger) => {
    trigger.addEventListener("click", () => {
      const modalType = trigger.dataset.modal;
      const template = document.getElementById(`template-${modalType}`);
      if (template && modalContainer) {
        modalTitle.textContent = modalTitles[modalType] || "条件を選択";
        modalBody.innerHTML = "";
        modalBody.appendChild(template.content.cloneNode(true));

        // Initialize Japan Map if it's the map modal
        if (modalType === "map") {
          // Use setTimeout ensure DOM is ready
          setTimeout(() => {
            if (typeof window.reinitJapanMap === "function") {
              window.reinitJapanMap();
            }
          }, 50);
        }

        // Sync modal inputs with existing hidden inputs in main form
        const modalInputs = modalBody.querySelectorAll(".modal-sync-input");
        modalInputs.forEach((modalInput) => {
          const name = modalInput.name;
          const value = modalInput.value;
          const existing = mainForm.querySelector(
            `input[name="${name}"][value="${value}"]`,
          );
          if (existing) modalInput.checked = true;
        });

        modalContainer.classList.remove("hidden");
        document.body.style.overflow = "hidden";
      }
    });
  });

  // Handle changes in modal and sync to main form
  if (modalBody) {
    modalBody.addEventListener("change", (e) => {
      if (e.target.classList.contains("modal-sync-input")) {
        const input = e.target;
        const name = input.name;
        const value = input.value;
        const type = input.type;

        if (type === "checkbox") {
          if (input.checked) {
            if (
              !mainForm.querySelector(`input[name="${name}"][value="${value}"]`)
            ) {
              const hidden = document.createElement("input");
              hidden.type = "hidden";
              hidden.name = name;
              hidden.value = value;
              mainForm.appendChild(hidden);
            }
          } else {
            const hidden = mainForm.querySelector(
              `input[name="${name}"][value="${value}"]`,
            );
            if (hidden) hidden.remove();
          }
        } else if (type === "radio") {
          // Remove existing radios of same name
          mainForm
            .querySelectorAll(`input[name="${name}"]`)
            .forEach((el) => el.remove());
          if (input.checked) {
            const hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = name;
            hidden.value = value;
            mainForm.appendChild(hidden);
          }
        }
      }
    });
  }

  closeBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      if (modalContainer) {
        modalContainer.classList.add("hidden");
        document.body.style.overflow = "";
      }
    });
  });

  // Initialize Japan Map Logic
  let japanMapInstance = null;

  // Define global re-init function
  window.reinitJapanMap = function () {
    // Always create new instance as DOM is recreated
    const container = document.getElementById("japan-map-container");
    if (container) {
      // Check if JapanMap is loaded
      if (typeof window.JapanMap !== "undefined") {
        japanMapInstance = new window.JapanMap("japan-map-container");

        container.addEventListener("prefectureSelected", (e) => {
          const pref = e.detail;
          showMunicipalityModal(pref);
        });
      }
    }
  };

  // Municipality Modal
  function showMunicipalityModal(prefecture) {
    const modalContainer = document.getElementById("search-modal-container");
    const modalBody = document.getElementById("modal-body");

    if (!modalBody || !modalContainer) return;

    // Get municipalities for this prefecture
    let municipalities = [];
    const prefName = prefecture.name;
    const prefCode = prefecture.code; // Assuming 'code' exists in the pref object passed from japan-map.js

    // Try fetching from comprehensive ALL_MUNICIPALITIES_DATA first
    if (
      typeof window.ALL_MUNICIPALITIES_DATA !== "undefined" &&
      prefCode &&
      window.ALL_MUNICIPALITIES_DATA[prefCode]
    ) {
      municipalities = window.ALL_MUNICIPALITIES_DATA[prefCode];
    }
    // Fallback: Try Name in old data
    else if (window.MUNICIPALITIES_DATA) {
      if (window.MUNICIPALITIES_DATA[prefName]) {
        municipalities = window.MUNICIPALITIES_DATA[prefName];
      } else {
        const cleanName = prefName.replace(/[都府県]$/, "");
        if (window.MUNICIPALITIES_DATA[cleanName]) {
          municipalities = window.MUNICIPALITIES_DATA[cleanName];
        }
      }
    }

    // Create split view HTML
    const modalHTML = `
        <div class="flex flex-col md:flex-row h-[80vh] w-full overflow-hidden bg-white/95 rounded-3xl animate-in fade-in duration-300">
          <!-- Left: Map (Mini) -->
          <div class="relative w-full md:w-[40%] h-[250px] md:h-full bg-slate-50/50 border-b md:border-b-0 md:border-r border-slate-100 flex items-center justify-center p-4 overflow-hidden">
            <div id="japan-map-mini-container" class="w-full h-full relative" data-fixed-height="true"></div>
            
             <!-- Header Overlay -->
             <div class="absolute top-4 left-4 z-10 flex flex-col gap-1 pointer-events-none">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-white/80 backdrop-blur text-xs font-black text-slate-500 rounded-full shadow-sm border border-slate-200">
                        ${prefName}
                    </span>
                </div>
             </div>

             <!-- Back Button -->
             <button class="modal-back-btn absolute top-4 right-4 z-20 bg-white border border-slate-200 text-slate-500 hover:text-slate-800 p-2 rounded-xl shadow-sm hover:shadow active:scale-95 transition-all">
                <i class="fas fa-chevron-left"></i>
             </button>
          </div>

          <!-- Right: List -->
          <div class="flex-1 flex flex-col h-full overflow-hidden bg-white">
            <div class="p-4 md:p-6 border-b border-slate-100 shrink-0 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-black text-slate-800 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-indigo-500"></i> ${prefName}のエリア
                    </h2>
                    <p class="text-xs text-slate-400 font-bold mt-1">市区町村を選択してください</p>
                </div>
                <button id="toggle-all-munis" class="text-xs font-black text-cyan-600 bg-cyan-50 px-3 py-1.5 rounded-lg border border-cyan-100 hover:bg-cyan-100 transition-colors">
                    すべて選択
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 md:p-6 no-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="muni-grid">
                    ${municipalities
                      .map(
                        (muni) => `
                        <button class="muni-item group flex items-center gap-3 p-4 rounded-2xl border border-slate-100 hover:border-cyan-200 hover:bg-cyan-50/30 transition-all text-left active:scale-[0.98]" data-value="${muni}">
                            <div class="w-5 h-5 rounded border-2 border-slate-200 flex items-center justify-center bg-white group-hover:border-cyan-400 transition-colors muni-check">
                                <i class="fas fa-check text-[10px] text-white opacity-0 transition-opacity"></i>
                            </div>
                            <span class="text-sm font-bold text-slate-600 group-hover:text-cyan-700">${muni}</span>
                        </button>
                    `,
                      )
                      .join("")}
                </div>
            </div>

            <!-- Footer Action -->
            <div class="p-4 md:p-6 border-t border-slate-100 bg-slate-50/50 shrink-0">
                <button id="commit-munis" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black shadow-lg shadow-slate-200 hover:bg-slate-800 hover:shadow-xl active:scale-95 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span>この条件で検索する</span>
                    <span id="selected-count-badge" class="bg-white/20 px-2 py-0.5 rounded text-xs">0</span>
                </button>
            </div>
          </div>
        </div>
      `;

    modalBody.innerHTML = modalHTML;
    modalContainer.classList.remove("hidden"); // Ensure visible

    // Re-initialize Map in new container
    setTimeout(() => {
      if (typeof window.JapanMap !== "undefined") {
        const miniMap = new window.JapanMap("japan-map-mini-container");

        // Clean up previous instance if any
        // We can't access easily but JS GC handles if DOM is removed?

        // Programmatically zoom to the prefecture
        // We need to find the region first
        // JapanMap instance has regions loaded in init(), but it's async...
        // Wait for it to be ready? JapanMap.init is async.
        // Since SVG is fetched, we might need a small delay or event.
        // However, let's try a simple polling or hook.
        // For now, let's just wait a bit longer or modify JapanMap to emit "ready".

        // Alternative: The main map was already loaded, so SVG is in cache?
        // JapanMap fetches SVG every time.

        const tryZoom = setInterval(() => {
          if (miniMap.regions.length > 0 && miniMap.svgElement) {
            clearInterval(tryZoom);
            // Find region
            const region = miniMap.regions.find((r) =>
              r.prefectures.some((p) => p.name === prefName),
            );
            if (region) {
              miniMap.selectRegion(region); // This colors it
              // Find pref ID
              const prefData = region.prefectures.find(
                (p) => p.name === prefName,
              );
              if (prefData) {
                miniMap.selectPrefecture(prefData); // Highlights it
                if (miniMap.zoomToPrefecture) {
                  miniMap.zoomToPrefecture(prefData.id);
                }
              }
            }
          }
        }, 100);
      }
    }, 50);

    // --- Logic for Selection ---
    const muniItems = modalBody.querySelectorAll(".muni-item");
    const toggleAllBtn = document.getElementById("toggle-all-munis");
    const commitBtn = document.getElementById("commit-munis");
    const countBadge = document.getElementById("selected-count-badge");
    let selectedmc = [];

    function updateUI() {
      muniItems.forEach((item) => {
        const val = item.dataset.value;
        const isSel = selectedmc.includes(val);
        const check = item.querySelector(".muni-check");
        const icon = check.querySelector("i");

        if (isSel) {
          item.classList.add(
            "bg-cyan-500",
            "border-cyan-500",
            "text-white",
            "shadow-md",
            "shadow-cyan-200",
          );
          item.classList.remove("hover:bg-cyan-50/30", "bg-white");
          check.classList.add("bg-white", "border-white");
          check.classList.remove("bg-white", "border-slate-200");
          icon.classList.remove("opacity-0");
          icon.classList.add("text-cyan-500");
          item.querySelector("span").classList.add("text-white");
          item
            .querySelector("span")
            .classList.remove("text-slate-600", "group-hover:text-cyan-700");
        } else {
          item.classList.remove(
            "bg-cyan-500",
            "border-cyan-500",
            "text-white",
            "shadow-md",
            "shadow-cyan-200",
          );
          item.classList.add("bg-white", "hover:bg-cyan-50/30");
          check.classList.remove("bg-white", "border-white");
          check.classList.add("bg-white", "border-slate-200");
          icon.classList.add("opacity-0");
          icon.classList.remove("text-cyan-500");
          item.querySelector("span").classList.remove("text-white");
          item.querySelector("span").classList.add("text-slate-600");
        }
      });

      countBadge.textContent = selectedmc.length;
      toggleAllBtn.textContent =
        selectedmc.length === municipalities.length
          ? "すべて解除"
          : "すべて選択";

      if (selectedmc.length === 0) {
        commitBtn.innerHTML = `<span>${prefName}全体で検索</span>`;
      } else {
        commitBtn.innerHTML = `<span>この条件で検索する</span> <span class="bg-white/20 px-2 py-0.5 rounded text-xs">${selectedmc.length}</span>`;
      }
    }

    muniItems.forEach((item) => {
      item.addEventListener("click", () => {
        const val = item.dataset.value;
        if (selectedmc.includes(val)) {
          selectedmc = selectedmc.filter((v) => v !== val);
        } else {
          selectedmc.push(val);
        }
        updateUI();
      });
    });

    toggleAllBtn.addEventListener("click", () => {
      if (selectedmc.length === municipalities.length) {
        selectedmc = [];
      } else {
        selectedmc = [...municipalities];
      }
      updateUI();
    });

    // Back Button
    const backBtn = modalBody.querySelector(".modal-back-btn");
    backBtn.addEventListener("click", () => {
      // Restore map template
      const template = document.getElementById("template-map");
      if (template) {
        modalBody.innerHTML = "";
        modalBody.appendChild(template.content.cloneNode(true));
        // Re-init main map
        setTimeout(() => {
          if (typeof window.reinitJapanMap === "function")
            window.reinitJapanMap();
        }, 50);
      }
    });

    // Commit Button
    commitBtn.addEventListener("click", () => {
      const mainForm = document.getElementById("main-search-form");
      if (!mainForm) return;

      // Clear existing loc inputs (pref/muni)
      // Assuming we want fresh start for this search
      // Or should we append? Usually "Area Search" implies replacing old area constraints.
      // Let's remove existing pref/muni inputs first to avoid duplicates or conflicts
      const existingPrefs = mainForm.querySelectorAll('input[name="pref[]"]');
      existingPrefs.forEach((el) => el.remove());
      const existingMunis = mainForm.querySelectorAll('input[name="muni[]"]'); // If any
      existingMunis.forEach((el) => el.remove());

      // Add Prefecture
      const pInput = document.createElement("input");
      pInput.type = "hidden";
      pInput.name = "pref"; // Note: WP query usually expects 'pref' or 'pref[]' depending on implementation. In archive-job.php it checked $_GET['pref'] (singular).
      // Wait, archive-job.php: $selected_pref = isset($_GET['pref']) ...
      // It seems it handles single pref?
      // But the modal implies multiple?
      // Let's check archive-job.php line 43 <select name="pref">. Single.

      // If single, we just set 'pref'.
      pInput.name = "pref";
      pInput.value = prefName;
      mainForm.appendChild(pInput);

      // Add Municipalities (if any)
      // archive-job.php doesn't seem to handle municipalities in the provided snippet!
      // It only has 'pref' filter.
      // But the USER REQUESTED to replicate the municipality screen.
      // Maybe I should add hidden inputs for 'muni[]' anyway, even if PHP doesn't handle it yet?
      // The user said "Do not change any other existing features", but this is a new feature.
      // I will add them.
      selectedmc.forEach((m) => {
        const mInput = document.createElement("input");
        mInput.type = "hidden";
        mInput.name = "muni[]"; // Or keywords?
        mInput.value = m;
        mainForm.appendChild(mInput);
      });

      // Also add to keywords if PHP doesn't handle 'muni'?
      // Usually keyword search covers text.
      if (selectedmc.length > 0) {
        // Maybe append to keyword?
        // Not safe.
      }

      // Submit or Close?
      // The modal usually syncs form. Submitting is done by "Search" button on main page?
      // But this button says "Search with this condition".
      // JapanMap.tsx has onMunicipalitiesSelect.
      // If I emulate "Search", I should submit the form.
      mainForm.submit();
    });
  }
});
