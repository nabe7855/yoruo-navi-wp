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
        if (
          modalType === "map" &&
          typeof window.reinitJapanMap === "function"
        ) {
          window.reinitJapanMap();
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

  // Initialize Japan Map when map modal is opened
  let japanMapInstance = null;

  const mapModalTrigger = document.querySelector('[data-modal="map"]');
  if (mapModalTrigger) {
    mapModalTrigger.addEventListener("click", () => {
      // Wait for modal to be visible
      setTimeout(() => {
        if (!japanMapInstance) {
          japanMapInstance = new window.JapanMap("japan-map-container");

          // Listen for prefecture selection
          const mapContainer = document.getElementById("japan-map-container");
          if (mapContainer) {
            mapContainer.addEventListener("prefectureSelected", (e) => {
              const pref = e.detail;
              showMunicipalityModal(pref);
            });
          }
        }
      }, 100);
    });
  }

  // Municipality Modal
  function showMunicipalityModal(prefecture) {
    const modalContainer = document.getElementById("modal-container");
    const modalBody = document.getElementById("modal-body");

    if (!modalBody || !modalContainer) return;

    // Get municipalities for this prefecture
    const municipalities = window.MUNICIPALITIES_DATA?.[prefecture.name] || [];

    // Create municipality modal HTML
    const modalHTML = `
      <div class="fixed inset-0 z-50 bg-white flex flex-col animate-in slide-in-from-bottom-5 duration-300">
        <!-- Header -->
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-white/80 backdrop-blur-md sticky top-0 z-10">
          <button class="modal-back-btn p-2 -ml-2 text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition">
            <i class="fas fa-chevron-left text-xl"></i>
          </button>
          
          <h2 class="font-black text-lg text-slate-800 flex items-center gap-2">
            <i class="fas fa-map-marker-alt text-indigo-600"></i>
            ${prefecture.name}
          </h2>
          
          <button class="modal-close-btn p-2 -mr-2 text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition">
            <i class="fas fa-times text-xl"></i>
          </button>
        </div>

        <!-- Search Bar -->
        <div class="p-4 bg-slate-50 border-b border-slate-100">
          <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input
              type="text"
              id="municipality-search"
              placeholder="${prefecture.name}の市区町村を検索..."
              class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition font-bold text-slate-700 bg-white shadow-sm"
            />
          </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto bg-white p-4 pb-20">
          <div class="space-y-8">
            <!-- All Cities -->
            <div>
              <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">
                All Cities
              </h3>
              <div id="municipality-list" class="divide-y divide-slate-50 border-t border-b border-slate-50">
                ${municipalities
                  .map(
                    (city) => `
                  <button
                    class="municipality-item w-full py-4 px-2 flex justify-between items-center hover:bg-slate-50 active:bg-slate-100 transition group"
                    data-city="${city}"
                    data-pref="${prefecture.name}"
                  >
                    <span class="font-bold text-slate-700 text-lg group-hover:text-indigo-600 transition">
                      ${city}
                    </span>
                    <i class="fas fa-chevron-right text-slate-300 group-hover:text-indigo-400 transform group-hover:translate-x-1 transition"></i>
                  </button>
                `,
                  )
                  .join("")}
              </div>
            </div>
          </div>
        </div>
      </div>
    `;

    modalBody.innerHTML = modalHTML;

    // Setup search functionality
    const searchInput = document.getElementById("municipality-search");
    const municipalityList = document.getElementById("municipality-list");

    if (searchInput && municipalityList) {
      searchInput.addEventListener("input", (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const items = municipalityList.querySelectorAll(".municipality-item");

        items.forEach((item) => {
          const cityName = item.dataset.city.toLowerCase();
          if (cityName.includes(searchTerm)) {
            item.style.display = "";
          } else {
            item.style.display = "none";
          }
        });
      });
    }

    // Setup municipality selection
    const municipalityItems = modalBody.querySelectorAll(".municipality-item");
    municipalityItems.forEach((item) => {
      item.addEventListener("click", () => {
        const city = item.dataset.city;
        const pref = item.dataset.pref;

        // Add to search form
        const mainForm = document.getElementById("main-search-form");
        if (mainForm) {
          // Add prefecture
          if (
            !mainForm.querySelector(`input[name="pref[]"][value="${pref}"]`)
          ) {
            const prefInput = document.createElement("input");
            prefInput.type = "hidden";
            prefInput.name = "pref[]";
            prefInput.value = pref;
            mainForm.appendChild(prefInput);
          }

          // Add municipality
          if (
            !mainForm.querySelector(`input[name="muni[]"][value="${city}"]`)
          ) {
            const muniInput = document.createElement("input");
            muniInput.type = "hidden";
            muniInput.name = "muni[]";
            muniInput.value = city;
            mainForm.appendChild(muniInput);
          }
        }

        // Close modal
        modalContainer.classList.add("hidden");
        document.body.style.overflow = "";

        // Show success message (optional)
        console.log(`Selected: ${pref} - ${city}`);
      });
    });

    // Setup back button
    const backBtn = modalBody.querySelector(".modal-back-btn");
    if (backBtn) {
      backBtn.addEventListener("click", () => {
        // Go back to map view
        const template = document.getElementById("template-map");
        if (template) {
          modalBody.innerHTML = template.innerHTML;

          // Re-initialize map
          setTimeout(() => {
            if (japanMapInstance) {
              japanMapInstance.resetSelection();
            } else {
              japanMapInstance = new window.JapanMap("japan-map-container");

              const mapContainer = document.getElementById(
                "japan-map-container",
              );
              if (mapContainer) {
                mapContainer.addEventListener("prefectureSelected", (e) => {
                  const pref = e.detail;
                  showMunicipalityModal(pref);
                });
              }
            }
          }, 100);
        }
      });
    }

    // Setup close button
    const closeBtn = modalBody.querySelector(".modal-close-btn");
    if (closeBtn) {
      closeBtn.addEventListener("click", () => {
        modalContainer.classList.add("hidden");
        document.body.style.overflow = "";
      });
    }
  }
});
