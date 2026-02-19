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

  // --- Area Selection State ---
  let currentModalType = null;
  let selectedAreaState = {
    region: null, // { id, name }
    pref: null, // { id, name, code }
    munis: [], // string[]
    view: "map", // 'map' or 'municipality'
  };

  const modalTitles = {
    map: "エリアで探す",
    "job-type": "職種で探す",
    salary: "給与で探す",
    "work-style": "働き方で探す",
  };

  triggers.forEach((trigger) => {
    trigger.addEventListener("click", () => {
      const modalType = trigger.dataset.modal;
      currentModalType = modalType;

      const template = document.getElementById(`template-${modalType}`);
      if (template && modalContainer) {
        modalTitle.textContent = modalTitles[modalType] || "条件を選択";
        modalBody.innerHTML = "";
        modalBody.appendChild(template.content.cloneNode(true));

        // Initialize Japan Map if it's the map modal
        if (modalType === "map") {
          selectedAreaState.view = "map";
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
              if (input.dataset.label)
                hidden.dataset.label = input.dataset.label;
              mainForm.appendChild(hidden);
              updateSelectedTags();
            }
          } else {
            const hidden = mainForm.querySelector(
              `input[name="${name}"][value="${value}"]`,
            );
            if (hidden) hidden.remove();
            updateSelectedTags();
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
            if (input.dataset.label) hidden.dataset.label = input.dataset.label;
            mainForm.appendChild(hidden);
            updateSelectedTags();
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
    const container = document.getElementById("japan-map-container");
    if (container) {
      if (typeof window.JapanMap !== "undefined") {
        japanMapInstance = new window.JapanMap("japan-map-container");

        container.addEventListener("regionSelected", (e) => {
          selectedAreaState.region = e.detail;
          selectedAreaState.pref = null;
          selectedAreaState.munis = [];
        });

        container.addEventListener("prefectureSelected", (e) => {
          const pref = e.detail;
          selectedAreaState.pref = pref;
          selectedAreaState.munis = [];
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

    selectedAreaState.view = "municipality";

    // Get municipalities for this prefecture
    let municipalities = [];
    const prefName = prefecture.name;
    const prefCode = prefecture.code;

    if (
      typeof window.ALL_MUNICIPALITIES_DATA !== "undefined" &&
      prefCode &&
      window.ALL_MUNICIPALITIES_DATA[prefCode]
    ) {
      municipalities = window.ALL_MUNICIPALITIES_DATA[prefCode];
    } else if (window.MUNICIPALITIES_DATA) {
      if (window.MUNICIPALITIES_DATA[prefName]) {
        municipalities = window.MUNICIPALITIES_DATA[prefName];
      } else {
        const cleanName = prefName.replace(/[都府県]$/, "");
        if (window.MUNICIPALITIES_DATA[cleanName]) {
          municipalities = window.MUNICIPALITIES_DATA[cleanName];
        }
      }
    }

    const modalHTML = `
        <div class="flex flex-col md:flex-row h-[80vh] w-full overflow-hidden bg-white/95 rounded-3xl animate-in fade-in duration-300">
          <!-- Left: Map (Mini) -->
          <div class="relative w-full md:w-[40%] h-[250px] md:h-full bg-slate-50/50 border-b md:border-b-0 md:border-r border-slate-100 flex items-center justify-center p-4 overflow-hidden">
            <div id="japan-map-mini-container" class="w-full h-full relative" data-fixed-height="true"></div>
            
             <div class="absolute top-4 left-4 z-10 flex flex-col gap-1 pointer-events-none">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-white/80 backdrop-blur text-xs font-black text-slate-500 rounded-full shadow-sm border border-slate-200">
                        ${prefName}
                    </span>
                </div>
             </div>

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
          </div>
        </div>
      `;

    modalBody.innerHTML = modalHTML;
    modalContainer.classList.remove("hidden");

    setTimeout(() => {
      if (typeof window.JapanMap !== "undefined") {
        const miniMap = new window.JapanMap("japan-map-mini-container");
        const tryZoom = setInterval(() => {
          if (miniMap.regions.length > 0 && miniMap.svgElement) {
            clearInterval(tryZoom);
            const region = miniMap.regions.find((r) =>
              r.prefectures.some((p) => p.name === prefName),
            );
            if (region) {
              miniMap.selectRegion(region);
              const prefData = region.prefectures.find(
                (p) => p.name === prefName,
              );
              if (prefData) {
                miniMap.selectPrefecture(prefData);
                if (miniMap.zoomToPrefecture) {
                  miniMap.zoomToPrefecture(prefData.id);
                }
              }
            }
          }
        }, 100);
      }
    }, 50);

    const muniItems = modalBody.querySelectorAll(".muni-item");
    const toggleAllBtn = document.getElementById("toggle-all-munis");
    const mainForm = document.getElementById("main-search-form");

    // Hydrate
    if (mainForm) {
      const existingMunis = mainForm.querySelectorAll('input[name="muni[]"]');
      existingMunis.forEach((input) => {
        if (!selectedAreaState.munis.includes(input.value)) {
          selectedAreaState.munis.push(input.value);
        }
      });
    }

    function updateUI() {
      muniItems.forEach((item) => {
        const val = item.dataset.value;
        const isSel = selectedAreaState.munis.includes(val);
        const check = item.querySelector(".muni-check");
        const icon = check.querySelector("i");
        const span = item.querySelector("span");

        if (isSel) {
          item.className =
            "muni-item group flex items-center gap-3 p-4 rounded-2xl border border-cyan-500 bg-cyan-500 text-white shadow-md shadow-cyan-200 transition-all text-left active:scale-[0.98]";
          check.className =
            "w-5 h-5 rounded border-2 border-white flex items-center justify-center bg-white muni-check";
          icon.className = "fas fa-check text-[10px] text-cyan-500";
          span.className = "text-sm font-bold text-white";
        } else {
          item.className =
            "muni-item group flex items-center gap-3 p-4 rounded-2xl border border-slate-100 hover:border-cyan-200 hover:bg-cyan-50/30 bg-white transition-all text-left active:scale-[0.98]";
          check.className =
            "w-5 h-5 rounded border-2 border-slate-200 flex items-center justify-center bg-white group-hover:border-cyan-400 transition-colors muni-check";
          icon.className =
            "fas fa-check text-[10px] text-white opacity-0 transition-opacity";
          span.className = "text-sm font-bold text-slate-600";
        }
      });

      toggleAllBtn.textContent =
        selectedAreaState.munis.length === municipalities.length
          ? "すべて解除"
          : "すべて選択";
    }

    muniItems.forEach((item) => {
      item.addEventListener("click", () => {
        const val = item.dataset.value;
        if (selectedAreaState.munis.includes(val)) {
          selectedAreaState.munis = selectedAreaState.munis.filter(
            (v) => v !== val,
          );
        } else {
          selectedAreaState.munis.push(val);
        }
        updateUI();
      });
    });

    updateUI();

    toggleAllBtn.addEventListener("click", () => {
      if (selectedAreaState.munis.length === municipalities.length) {
        selectedAreaState.munis = [];
      } else {
        selectedAreaState.munis = [...municipalities];
      }
      updateUI();
    });

    // Back Button
    const backBtn = modalBody.querySelector(".modal-back-btn");
    backBtn.addEventListener("click", () => {
      selectedAreaState.view = "map";
      // pref is kept for map context but ideally cleared if zoomed out
      const template = document.getElementById("template-map");
      if (template) {
        modalBody.innerHTML = "";
        modalBody.appendChild(template.content.cloneNode(true));
        setTimeout(() => {
          if (typeof window.reinitJapanMap === "function")
            window.reinitJapanMap();
        }, 50);
      }
    });
  }

  // --- Main Modal Confirm Button ---
  const modalSubmitBtn = document.getElementById("modal-submit-btn");
  if (modalSubmitBtn) {
    modalSubmitBtn.addEventListener("click", () => {
      if (currentModalType === "map") {
        const mainForm = document.getElementById("main-search-form");
        if (!mainForm) return;

        // Clear existing area inputs
        const fields = ["state", "pref", "muni[]"];
        fields.forEach((name) => {
          mainForm.querySelectorAll(`input[name="${name}"]`).forEach((el) => {
            el.remove();
          });
        });

        // Determine priority
        if (
          selectedAreaState.view === "municipality" &&
          selectedAreaState.munis.length > 0
        ) {
          // 1. Search by Municipalities
          const pInput = document.createElement("input");
          pInput.type = "hidden";
          pInput.name = "pref";
          pInput.value = selectedAreaState.pref.name;
          pInput.dataset.label = selectedAreaState.pref.name;
          mainForm.appendChild(pInput);

          selectedAreaState.munis.forEach((m) => {
            const mInput = document.createElement("input");
            mInput.type = "hidden";
            mInput.name = "muni[]";
            mInput.value = m;
            mInput.dataset.label = m;
            mainForm.appendChild(mInput);
          });
        } else if (selectedAreaState.pref) {
          // 2. Search by Prefecture
          const pInput = document.createElement("input");
          pInput.type = "hidden";
          pInput.name = "pref";
          pInput.value = selectedAreaState.pref.name;
          pInput.dataset.label = selectedAreaState.pref.name;
          mainForm.appendChild(pInput);
        } else if (selectedAreaState.region) {
          // 3. Search by Region
          const rInput = document.createElement("input");
          rInput.type = "hidden";
          rInput.name = "state";
          rInput.value = selectedAreaState.region.id; // use ID for filtering
          rInput.dataset.label = selectedAreaState.region.name;
          mainForm.appendChild(rInput);
        }

        updateSelectedTags();
      }

      // Standard close logic
      if (modalContainer) {
        modalContainer.classList.add("hidden");
        document.body.style.overflow = "";
      }
    });
  }

  // --- Selected Tags Logic ---
  function updateSelectedTags() {
    const container = document.getElementById("selected-tags-container");
    const mainForm = document.getElementById("main-search-form");
    if (!container || !mainForm) return;

    container.innerHTML = "";

    // 1. Checkboxed tags (Quick tags)
    const quickTags = mainForm.querySelectorAll('input[name="tags[]"]:checked');
    quickTags.forEach((input) => {
      // Find label text from the span next to it or data-label
      const labelText =
        input.dataset.label ||
        (input.nextElementSibling
          ? input.nextElementSibling.textContent.trim()
          : input.value);
      addTag(container, labelText, input);
    });

    // 2. Hidden inputs (category, salary, style, pref, muni)
    const hiddenInputs = mainForm.querySelectorAll(
      'input[type="hidden"][name="category[]"], input[type="hidden"][name="salary"], input[type="hidden"][name="style[]"], input[type="hidden"][name="state"], input[type="hidden"][name="pref"], input[type="hidden"][name="muni[]"]',
    );

    hiddenInputs.forEach((input) => {
      const label = input.dataset.label || input.value;
      if (label) addTag(container, label, input);
    });
  }

  function addTag(container, label, inputElement) {
    if (!label) return;

    const name = inputElement.name;
    let icon = "fa-tag";
    let colorClass =
      "bg-indigo-50 text-indigo-700 border-indigo-100 hover:bg-indigo-100";
    let iconColor = "text-indigo-400";

    if (name === "category[]") {
      icon = "fa-briefcase";
      colorClass =
        "bg-indigo-50 text-indigo-700 border-indigo-100 hover:bg-indigo-100";
      iconColor = "text-indigo-400";
    } else if (name === "salary") {
      icon = "fa-wallet";
      colorClass =
        "bg-emerald-50 text-emerald-700 border-emerald-100 hover:bg-emerald-100";
      iconColor = "text-emerald-400";
    } else if (name === "style[]") {
      icon = "fa-layer-group";
      colorClass = "bg-blue-50 text-blue-700 border-blue-100 hover:bg-blue-100";
      iconColor = "text-blue-400";
    } else if (name === "pref" || name === "muni[]") {
      icon = "fa-map-marker-alt";
      colorClass = "bg-cyan-50 text-cyan-700 border-cyan-100 hover:bg-cyan-100";
      iconColor = "text-cyan-400";
    } else if (name === "tags[]") {
      icon = "fa-bolt";
      colorClass =
        "bg-amber-50 text-amber-700 border-amber-100 hover:bg-amber-100";
      iconColor = "text-amber-400";
    }

    const tag = document.createElement("button");
    tag.type = "button";
    tag.className = `flex items-center gap-2 px-3 py-1.5 ${colorClass} border rounded-xl text-[10px] md:text-xs font-black transition-all group active:scale-95 animate-nurutto`;
    tag.innerHTML = `
            <i class="fas ${icon} ${iconColor} group-hover:scale-110 transition-transform"></i>
            <span>${label}</span>
            <i class="fas fa-times opacity-30 group-hover:opacity-100 group-hover:text-red-500 transition-all ml-1"></i>
        `;

    tag.addEventListener("click", (e) => {
      e.stopPropagation();
      if (inputElement.type === "checkbox" || inputElement.type === "radio") {
        inputElement.checked = false;
        inputElement.dispatchEvent(new Event("change", { bubbles: true }));
      } else {
        const inputName = inputElement.name;
        const inputValue = inputElement.value;
        inputElement.remove();

        const modalInput = document.querySelector(
          `.modal-sync-input[name="${inputName}"][value="${inputValue}"]`,
        );
        if (modalInput) {
          modalInput.checked = false;
          modalInput.dispatchEvent(new Event("change", { bubbles: true }));
        }
      }
      updateSelectedTags();
    });

    container.appendChild(tag);
  }

  // Listen for changes and update tags
  if (mainForm) {
    mainForm.addEventListener("change", () => {
      updateSelectedTags();
    });
    // Initial update
    updateSelectedTags();
  }

  // --- 30 Second Diagnosis (Matcher) Logic ---
  const matcherTriggers = document.querySelectorAll(".matcher-trigger");
  const matcherModal = document.getElementById("matcher-modal-container");
  const matcherChatHistory = document.getElementById("matcher-chat-history");
  const matcherOptionsGrid = document.getElementById("matcher-options-grid");
  const matcherResultsView = document.getElementById("matcher-results-view");
  const matcherCloseBtns = document.querySelectorAll(
    ".matcher-modal-close, .matcher-modal-overlay",
  );

  let matcherStep = 0;
  let matcherAnswers = {};

  const REGIONS_DATA = {
    北海道: ["北海道"],
    東北: ["青森県", "岩手県", "宮城県", "秋田県", "山形県", "福島県"],
    関東: [
      "東京都",
      "神奈川県",
      "埼玉県",
      "千葉県",
      "茨城県",
      "栃木県",
      "群馬県",
    ],
    中部: [
      "愛知県",
      "静岡県",
      "岐阜県",
      "三重県",
      "新潟県",
      "富山県",
      "石川県",
      "福井県",
      "山梨県",
      "長野県",
    ],
    関西: ["大阪府", "兵庫県", "京都府", "滋賀県", "奈良県", "和歌山県"],
    中国: ["鳥取県", "島根県", "岡山県", "広島県", "山口県"],
    四国: ["徳島県", "香川県", "愛媛県", "高知県"],
    "九州・沖縄": [
      "福岡県",
      "佐賀県",
      "長崎県",
      "熊本県",
      "大分県",
      "宮崎県",
      "鹿児島県",
      "沖縄県",
    ],
  };

  const MATCHER_QUESTIONS = [
    {
      id: "pref",
      question: "まずは、希望の勤務エリアを教えてください！",
      options: Object.keys(REGIONS_DATA),
      type: "region-selector",
    },
    {
      id: "salary",
      question: "希望する月収の目安は？",
      options: ["月給30万円〜", "月給50万円〜", "月給80万円〜", "即日日払いOK"],
    },
    {
      id: "shift",
      question: "働き方の希望はありますか？",
      options: ["自由シフト", "週1日からOK", "日払いOK", "昇給随時"],
    },
    {
      id: "personality",
      question: "自分の強みを選ぶならどれ？",
      options: ["未経験歓迎", "経験者優遇", "ノルマなし", "託児所あり"],
    },
  ];

  let selectedMatcherRegion = null;
  let selectedMatcherPref = null;
  let selectedMatcherCities = [];

  const PREF_NAME_TO_CODE = {
    北海道: "01",
    青森県: "02",
    岩手県: "03",
    宮城県: "04",
    秋田県: "05",
    山形県: "06",
    福島県: "07",
    茨城県: "08",
    栃木県: "09",
    群馬県: "10",
    埼玉県: "11",
    千葉県: "12",
    東京都: "13",
    神奈川県: "14",
    新潟県: "15",
    富山県: "16",
    石川県: "17",
    福井県: "18",
    山梨県: "19",
    長野県: "20",
    岐阜県: "21",
    静岡県: "22",
    愛知県: "23",
    三重県: "24",
    滋賀県: "25",
    京都府: "26",
    大阪府: "27",
    兵庫県: "28",
    奈良県: "29",
    和歌山県: "30",
    鳥取県: "31",
    島根県: "32",
    岡山県: "33",
    広島県: "34",
    山口県: "35",
    徳島県: "36",
    香川県: "37",
    愛媛県: "38",
    高知県: "39",
    福岡県: "40",
    佐賀県: "41",
    長崎県: "42",
    熊本県: "43",
    大分県: "44",
    宮崎県: "45",
    鹿児島県: "46",
    沖縄県: "47",
  };

  function openMatcher() {
    matcherStep = 0;
    matcherAnswers = {};
    selectedMatcherRegion = null;
    selectedMatcherPref = null;
    selectedMatcherCities = [];
    if (matcherChatHistory) matcherChatHistory.innerHTML = "";
    if (matcherResultsView) matcherResultsView.classList.add("hidden");
    if (matcherModal) matcherModal.classList.remove("hidden");
    document.body.style.overflow = "hidden";

    addBotMessage(
      "こんにちは！あなたの希望に合わせて、30秒でぴったりの仕事を提案します。",
    );
    setTimeout(() => {
      showMatcherQuestion(0);
    }, 600);
  }

  function addBotMessage(text) {
    if (!matcherChatHistory) return;
    const msg = document.createElement("div");
    msg.className = "flex justify-start animate-fade-in";
    msg.innerHTML = `
      <div class="max-w-[85%] px-5 py-3 bg-white text-slate-800 rounded-2xl rounded-bl-none text-sm font-bold shadow-sm border border-slate-100 leading-relaxed">
        ${text}
      </div>
    `;
    matcherChatHistory.appendChild(msg);
    matcherChatHistory.scrollTop = matcherChatHistory.scrollHeight;
  }

  function addUserMessage(text) {
    if (!matcherChatHistory) return;
    const msg = document.createElement("div");
    msg.className = "flex justify-end animate-fade-in";
    msg.innerHTML = `
      <div class="max-w-[85%] px-5 py-3 bg-indigo-600 text-white rounded-2xl rounded-br-none text-sm font-bold shadow-md leading-relaxed">
        ${text}
      </div>
    `;
    matcherChatHistory.appendChild(msg);
    matcherChatHistory.scrollTop = matcherChatHistory.scrollHeight;
  }

  function showMatcherQuestion(step) {
    const q = MATCHER_QUESTIONS[step];

    if (!matcherOptionsGrid) return;
    matcherOptionsGrid.innerHTML = "";

    // --- Municipality Selection Mode ---
    if (q.type === "region-selector" && selectedMatcherPref) {
      const code = PREF_NAME_TO_CODE[selectedMatcherPref];
      const cities = window.ALL_MUNICIPALITIES_DATA
        ? window.ALL_MUNICIPALITIES_DATA[code] || []
        : [];

      // Header info
      const header = document.createElement("div");
      header.className =
        "col-span-2 p-2 mb-2 bg-indigo-50 rounded-xl border border-indigo-100 flex items-center justify-between";
      header.innerHTML = `
            <span class="text-[10px] font-black text-indigo-600 ml-2">${selectedMatcherPref}のエリアを選択</span>
            <button class="px-3 py-1 bg-white text-indigo-600 text-[10px] font-black rounded-lg border border-indigo-200 hover:bg-indigo-100 transition" id="matcher-muni-all">すべて選択</button>
        `;
      matcherOptionsGrid.appendChild(header);

      // Wrapper for cities to enable scrolling
      const citiesWrapper = document.createElement("div");
      citiesWrapper.className =
        "col-span-2 grid grid-cols-2 gap-2 max-h-[250px] overflow-y-auto no-scrollbar py-1 pr-1";

      // Muni buttons
      cities.forEach((city) => {
        const isSelected = selectedMatcherCities.includes(city);
        const btn = document.createElement("button");
        btn.className = `px-2 py-3 border-2 rounded-xl font-bold text-[10px] transition-all flex items-center gap-2 ${isSelected ? "bg-indigo-600 border-indigo-600 text-white shadow-sm" : "bg-white border-slate-100 text-slate-700 hover:border-indigo-400"}`;
        btn.innerHTML = `
                <div class="w-3 h-3 rounded-sm border ${isSelected ? "bg-white border-white" : "bg-slate-50 border-slate-200"} flex items-center justify-center shrink-0">
                    <i class="fas fa-check text-[8px] ${isSelected ? "text-indigo-600" : "text-transparent"}"></i>
                </div>
                <span class="truncate">${city}</span>
            `;
        btn.onclick = () => {
          if (selectedMatcherCities.includes(city)) {
            selectedMatcherCities = selectedMatcherCities.filter(
              (c) => c !== city,
            );
          } else {
            selectedMatcherCities.push(city);
          }
          showMatcherQuestion(step);
        };
        citiesWrapper.appendChild(btn);
      });
      matcherOptionsGrid.appendChild(citiesWrapper);

      // "All Select" Toggle Logic
      const allBtn = document.getElementById("matcher-muni-all");
      if (allBtn) {
        allBtn.onclick = (e) => {
          e.stopPropagation();
          if (selectedMatcherCities.length === cities.length) {
            selectedMatcherCities = [];
          } else {
            selectedMatcherCities = [...cities];
          }
          showMatcherQuestion(step);
        };
      }

      // Action Buttons
      const footer = document.createElement("div");
      footer.className = "col-span-2 grid grid-cols-2 gap-3 mt-4";
      footer.innerHTML = `
            <button class="py-4 bg-slate-100 text-slate-500 font-black rounded-2xl hover:bg-slate-200 transition" id="matcher-muni-back">戻る</button>
            <button class="py-4 bg-indigo-600 text-white font-black rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition" id="matcher-muni-next">選択して次へ</button>
        `;
      matcherOptionsGrid.appendChild(footer);

      document.getElementById("matcher-muni-back").onclick = () => {
        selectedMatcherPref = null;
        selectedMatcherCities = [];
        showMatcherQuestion(step);
      };
      document.getElementById("matcher-muni-next").onclick = () => {
        handleMatcherAnswer(selectedMatcherPref); // Pass pref as the "answer" but we'll use cities too
      };

      return; // Don't run standard logic
    }

    // --- Prefecture/Region Selection Mode ---
    let options = q.options;
    if (q.type === "region-selector" && selectedMatcherRegion) {
      options = REGIONS_DATA[selectedMatcherRegion];
      // Add back button
      const backBtn = document.createElement("button");
      backBtn.className =
        "col-span-2 px-2 py-3 bg-slate-100 text-slate-500 font-black text-[10px] rounded-xl hover:bg-slate-200 transition active:scale-95 mb-1";
      backBtn.innerHTML =
        '<i class="fas fa-arrow-left mr-2"></i>地方選択に戻る';
      backBtn.onclick = () => {
        selectedMatcherRegion = null;
        showMatcherQuestion(step);
      };
      matcherOptionsGrid.appendChild(backBtn);
    } else {
      // Only show the question bubble if we haven't already OR if we just reset
      const lastMsg = matcherChatHistory.lastElementChild;
      if (!lastMsg || lastMsg.textContent.trim() !== q.question) {
        addBotMessage(q.question);
      }
    }

    options.forEach((opt) => {
      const btn = document.createElement("button");
      btn.className =
        "px-2 py-4 bg-slate-50 border-2 border-slate-100 text-slate-700 font-bold text-[11px] rounded-xl hover:bg-indigo-50 hover:border-indigo-600 hover:text-indigo-600 transition active:scale-95 shadow-sm";
      btn.textContent = opt;
      btn.onclick = () => {
        if (
          q.type === "region-selector" &&
          !selectedMatcherRegion &&
          opt !== "北海道"
        ) {
          selectedMatcherRegion = opt;
          showMatcherQuestion(step);
        } else if (q.type === "region-selector" && !selectedMatcherPref) {
          selectedMatcherPref = opt;
          showMatcherQuestion(step);
        } else {
          handleMatcherAnswer(opt);
        }
      };
      matcherOptionsGrid.appendChild(btn);
    });

    const stepInfo = document.getElementById("matcher-step-info");
    if (stepInfo) {
      stepInfo.textContent = `Question ${step + 1} / ${MATCHER_QUESTIONS.length}`;
    }
    updateMatcherProgressDots(step);
  }

  function updateMatcherProgressDots(step) {
    const container = document.getElementById("matcher-dots");
    if (!container) return;
    container.innerHTML = "";
    for (let i = 0; i < MATCHER_QUESTIONS.length; i++) {
      const dot = document.createElement("div");
      dot.className = `w-1.5 h-1.5 rounded-full transition-all ${
        i <= step ? "bg-indigo-600 scale-125" : "bg-slate-200"
      }`;
      container.appendChild(dot);
    }
  }

  function handleMatcherAnswer(answer) {
    const q = MATCHER_QUESTIONS[matcherStep];

    // Store regular answer
    matcherAnswers[q.id] = answer;

    // Handle area specially to include munis
    if (q.id === "pref") {
      matcherAnswers["muni"] = [...selectedMatcherCities];
      const displayAnswer =
        selectedMatcherCities.length > 0
          ? `${answer} (${selectedMatcherCities.length}エリア)`
          : answer;
      addUserMessage(displayAnswer);
    } else {
      addUserMessage(answer);
    }

    if (matcherOptionsGrid) matcherOptionsGrid.innerHTML = "";

    if (matcherStep < MATCHER_QUESTIONS.length - 1) {
      matcherStep++;
      selectedMatcherRegion = null;
      selectedMatcherPref = null; // Important: reset for next run if it re-enters
      setTimeout(() => {
        showMatcherQuestion(matcherStep);
      }, 600);
    } else {
      setTimeout(() => {
        finishMatcher();
      }, 800);
    }
  }

  function finishMatcher() {
    addBotMessage(
      "ありがとうございます！あなたにぴったりの求人を解析しました...",
    );
    setTimeout(() => {
      if (matcherResultsView) matcherResultsView.classList.remove("hidden");
    }, 1000);
  }

  matcherTriggers.forEach((trigger) => {
    trigger.addEventListener("click", (e) => {
      e.preventDefault();
      if (typeof closeRichMenu === "function") {
        closeRichMenu();
      }
      openMatcher();
    });
  });

  matcherCloseBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      if (matcherModal) matcherModal.classList.add("hidden");
      document.body.style.overflow = "";
    });
  });

  const restartBtn = document.getElementById("matcher-restart");
  if (restartBtn) {
    restartBtn.onclick = () => {
      openMatcher();
    };
  }

  const goResultsBtn = document.getElementById("matcher-go-results");
  if (goResultsBtn) {
    goResultsBtn.onclick = () => {
      const archiveUrl =
        document.querySelector('a[href*="/jobs/"]')?.href ||
        window.location.origin + "/jobs/";
      const params = new URLSearchParams();
      if (matcherAnswers.pref) params.set("pref", matcherAnswers.pref);
      if (matcherAnswers.salary) params.set("salary", matcherAnswers.salary);

      if (matcherAnswers.muni && matcherAnswers.muni.length > 0) {
        matcherAnswers.muni.forEach((m) => params.append("muni[]", m));
      }

      const tags = [];
      if (matcherAnswers.shift) tags.push(matcherAnswers.shift);
      if (matcherAnswers.personality) tags.push(matcherAnswers.personality);

      tags.forEach((t) => params.append("tags[]", t));

      window.location.href = `${archiveUrl}${
        archiveUrl.includes("?") ? "&" : "?"
      }${params.toString()}`;
    };
  }

  // --- Rich Hamburger Menu Logic ---
  const richMenuToggle = document.getElementById("menu-toggle");
  const richMenuContainer = document.getElementById("rich-menu-container");
  const richMenuPanel = document.getElementById("rich-menu-panel");
  const richMenuOverlay = document.querySelector(".rich-menu-overlay");
  const richMenuCloseBtns = document.querySelectorAll(
    ".rich-menu-close, .rich-menu-overlay",
  );

  function openRichMenu() {
    if (!richMenuContainer || !richMenuPanel || !richMenuOverlay) return;
    richMenuContainer.classList.remove("hidden");
    // Trigger animations after unhiding
    setTimeout(() => {
      richMenuOverlay.classList.remove("opacity-0");
      richMenuOverlay.classList.add("opacity-100");
      richMenuPanel.classList.remove("translate-x-full");
      richMenuPanel.classList.add("translate-x-0");
    }, 10);
    document.body.style.overflow = "hidden";
  }

  function closeRichMenu() {
    if (!richMenuContainer || !richMenuPanel || !richMenuOverlay) return;
    richMenuOverlay.classList.remove("opacity-100");
    richMenuOverlay.classList.add("opacity-0");
    richMenuPanel.classList.remove("translate-x-0");
    richMenuPanel.classList.add("translate-x-full");

    // Hide container after animation
    setTimeout(() => {
      richMenuContainer.classList.add("hidden");
    }, 500);
    document.body.style.overflow = "";
  }

  if (richMenuToggle) {
    richMenuToggle.addEventListener("click", (e) => {
      e.preventDefault();
      openRichMenu();
    });
  }

  richMenuCloseBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      closeRichMenu();
    });
  });

  // Handle link clicks inside rich menu to close it
  const richMenuLinks = document.querySelectorAll("#rich-menu-panel a");
  richMenuLinks.forEach((link) => {
    link.addEventListener("click", () => {
      // Don't close if it's a trigger for another modal
      if (!link.classList.contains("matcher-trigger")) {
        closeRichMenu();
      }
    });
  });
});
