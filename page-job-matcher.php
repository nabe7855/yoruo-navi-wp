<?php
/**
 * Template Name: 適職タイプ診断
 */

get_header(); ?>

<div id="job-matcher-app" class="bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 md:py-16 max-w-2xl">
        <!-- Chat Area -->
        <div id="chat-history" class="space-y-6 mb-32 md:mb-8 min-h-[400px]">
            <!-- Initial Message -->
            <div class="flex justify-start animate-fade-in">
                <div class="max-w-[85%] px-5 py-3 rounded-2xl text-sm font-black shadow-sm leading-relaxed bg-white text-gray-800 border border-indigo-50 rounded-bl-none">
                    こんにちは！あなたの希望に合わせて、30秒でぴったりの仕事を提案します。
                </div>
            </div>
            <div class="flex justify-start animate-fade-in">
                <div class="max-w-[85%] px-5 py-3 rounded-2xl text-sm font-black shadow-sm leading-relaxed bg-white text-gray-800 border border-indigo-50 rounded-bl-none">
                    まずは、希望の勤務エリアを教えてください！
                </div>
            </div>
        </div>

        <!-- Input Area (Fixed Bottom for Mobile) -->
        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-indigo-50 p-4 pb-10 md:relative md:bg-transparent md:border-0 md:p-0 md:shadow-none shadow-[0_-4px_20px_rgba(0,0,0,0.05)] z-50">
            <div id="options-grid" class="grid grid-cols-2 gap-3 mb-4">
                <!-- Options will be injected here -->
            </div>
            
            <div class="flex justify-between items-center px-1">
                <span id="step-counter" class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                    Question 1 / 5
                </span>
                <div id="step-dots" class="flex gap-1.5">
                    <!-- Dots will be injected here -->
                </div>
            </div>
        </div>

        <!-- Results Area (Hidden initially) -->
        <div id="matcher-results" class="hidden animate-fade-in">
            <div class="text-center mb-12">
                <div class="inline-block px-4 py-1 bg-indigo-600 text-white text-[10px] font-black rounded-full mb-4 uppercase tracking-widest">
                    MATCHING RESULT
                </div>
                <h2 class="text-3xl font-black text-gray-900 mb-2">あなたへの提案</h2>
                <p class="text-gray-500 font-bold">条件に最も近い求人をピックアップしました。</p>
            </div>

            <div id="matched-jobs-list" class="space-y-6">
                <!-- Matched jobs injected here -->
            </div>

            <div class="pt-10 border-t border-slate-200 flex flex-col md:flex-row justify-center gap-4 mt-12">
                <a href="<?php echo home_url(); ?>" class="px-8 py-4 bg-white text-slate-500 font-black rounded-2xl hover:bg-slate-50 transition border border-slate-100 text-center">
                    ホームへ戻る
                </a>
                <button onclick="restartDiagnosis()" class="px-8 py-4 bg-indigo-600 text-white font-black rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition">
                    もう一度診断する
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const REGIONS_DATA = {
    "北海道": ["北海道"],
    "東北": ["青森県", "岩手県", "宮城県", "秋田県", "山形県", "福島県"],
    "関東": ["東京都", "神奈川県", "埼玉県", "千葉県", "茨城県", "栃木県", "群馬県"],
    "中部": ["愛知県", "静岡県", "岐阜県", "三重県", "新潟県", "富山県", "石川県", "福井県", "山梨県", "長野県"],
    "関西": ["大阪府", "兵庫県", "京都府", "滋賀県", "奈良県", "和歌山県"],
    "中国": ["鳥取県", "島根県", "岡山県", "広島県", "山口県"],
    "四国": ["徳島県", "香川県", "愛媛県", "高知県"],
    "九州・沖縄": ["福岡県", "佐賀県", "長崎県", "熊本県", "大分県", "宮崎県", "鹿児島県", "沖縄県"]
};

const PREF_NAME_TO_CODE = {
    "北海道": "01", "青森県": "02", "岩手県": "03", "宮城県": "04", "秋田県": "05", "山形県": "06", "福島県": "07",
    "茨城県": "08", "栃木県": "09", "群馬県": "10", "埼玉県": "11", "千葉県": "12", "東京都": "13", "神奈川県": "14",
    "新潟県": "15", "富山県": "16", "石川県": "17", "福井県": "18", "山梨県": "19", "長野県": "20", "岐阜県": "21",
    "静岡県": "22", "愛知県": "23", "三重県": "24", "滋賀県": "25", "京都府": "26", "大阪府": "27", "兵庫県": "28",
    "奈良県": "29", "和歌山県": "30", "鳥取県": "31", "島根県": "32", "岡山県": "33", "広島県": "34", "山口県": "35",
    "徳島県": "36", "香川県": "37", "愛媛県": "38", "高知県": "39", "福岡県": "40", "佐賀県": "41", "長崎県": "42",
    "熊本県": "43", "大分県": "44", "宮崎県": "45", "鹿児島県": "46", "沖縄県": "47"
};

const MATCHING_QUESTIONS = [
    {
        id: "area",
        question: "まずは、希望の勤務エリアを教えてください！",
        options: Object.keys(REGIONS_DATA),
        type: "region-selector"
    },
    {
        id: "income",
        question: "希望する月収の目安は？",
        options: ["10万円〜", "20万円〜", "30万円〜", "50万円以上！"]
    },
    {
        id: "shift",
        question: "週に何日くらい働きたいですか？",
        options: ["週1〜2日", "週3〜4日", "週5日以上（ガッツリ）", "単発・不定期"]
    },
    {
        id: "time",
        question: "得意な時間帯はありますか？",
        options: ["昼・夕方", "夜（20時〜）", "深夜（24時〜）", "いつでもOK"]
    },
    {
        id: "personality",
        question: "自分の強みを選ぶならどれ？",
        options: ["聞き上手", "盛り上げ役", "真面目・正確", "おしゃれ・個性"]
    }
];

let currentStep = 0;
let answers = {};
let selectedRegion = null;
let selectedPref = null;
let selectedCities = [];

function renderOptions() {
    const grid = document.getElementById('options-grid');
    const question = MATCHING_QUESTIONS[currentStep];
    grid.innerHTML = '';
    
    // --- Municipality Selection Mode ---
    if (question.type === 'region-selector' && selectedPref) {
        const code = PREF_NAME_TO_CODE[selectedPref];
        const cities = window.ALL_MUNICIPALITIES_DATA ? window.ALL_MUNICIPALITIES_DATA[code] || [] : [];
        
        // Header
        const header = document.createElement('div');
        header.className = 'col-span-2 p-3 mb-2 bg-indigo-50 rounded-2xl border border-indigo-100 flex items-center justify-between';
        header.innerHTML = `
            <span class="text-xs font-black text-indigo-600 ml-2">${selectedPref}のエリアを選択</span>
            <button class="px-4 py-1.5 bg-white text-indigo-600 text-xs font-black rounded-xl border border-indigo-200 hover:bg-indigo-100 transition" id="muni-toggle-all">すべて選択</button>
        `;
        grid.appendChild(header);

        // Cities List with Scroll
        const citiesWrapper = document.createElement('div');
        citiesWrapper.className = 'col-span-2 grid grid-cols-2 gap-2 max-h-[250px] overflow-y-auto no-scrollbar py-1 pr-1';

        cities.forEach(city => {
            const isSelected = selectedCities.includes(city);
            const btn = document.createElement('button');
            btn.className = `px-2 py-3 border-2 rounded-2xl font-bold text-xs transition-all flex items-center gap-3 ${isSelected ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-white border-slate-100 text-slate-700 hover:border-indigo-400'}`;
            btn.innerHTML = `
                <div class="w-4 h-4 rounded border ${isSelected ? 'bg-white border-white' : 'bg-slate-50 border-slate-200'} flex items-center justify-center shrink-0">
                    <i class="fas fa-check text-[10px] ${isSelected ? 'text-indigo-600' : 'text-transparent'}"></i>
                </div>
                <span class="truncate">${city}</span>
            `;
            btn.onclick = () => {
                if (selectedCities.includes(city)) {
                    selectedCities = selectedCities.filter(c => c !== city);
                } else {
                    selectedCities.push(city);
                }
                renderOptions();
            };
            citiesWrapper.appendChild(btn);
        });
        grid.appendChild(citiesWrapper);

        document.getElementById('muni-toggle-all').onclick = () => {
            if (selectedCities.length === cities.length) {
                selectedCities = [];
            } else {
                selectedCities = [...cities];
            }
            renderOptions();
        };

        // Footer Actions
        const footer = document.createElement('div');
        footer.className = 'col-span-2 grid grid-cols-2 gap-3 mt-4';
        footer.innerHTML = `
            <button class="py-4 bg-slate-100 text-slate-500 font-black rounded-2xl hover:bg-slate-200 transition" id="muni-back-btn">戻る</button>
            <button class="py-4 bg-indigo-600 text-white font-black rounded-2xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition" id="muni-next-btn">選択して次へ</button>
        `;
        grid.appendChild(footer);

        document.getElementById('muni-back-btn').onclick = () => {
            selectedPref = null;
            selectedCities = [];
            renderOptions();
        };
        document.getElementById('muni-next-btn').onclick = () => {
            handleAnswer(selectedPref);
        };
        return;
    }

    // --- Region/Prefecture Mode ---
    let options = question.options;
    if (question.type === 'region-selector' && selectedRegion) {
        options = REGIONS_DATA[selectedRegion];
        const backBtn = document.createElement('button');
        backBtn.className = 'col-span-2 px-2 py-3 bg-slate-100 text-slate-500 font-black text-xs rounded-xl hover:bg-slate-200 transition active:scale-95 mb-2';
        backBtn.innerHTML = '<i class="fas fa-arrow-left mr-2"></i>地方選択に戻る';
        backBtn.onclick = () => {
            selectedRegion = null;
            renderOptions();
        };
        grid.appendChild(backBtn);
    }
    
    options.forEach(opt => {
        const btn = document.createElement('button');
        btn.className = 'px-2 py-4 bg-white border-2 border-slate-100 text-slate-700 font-black text-sm rounded-2xl hover:bg-indigo-50 hover:border-indigo-600 hover:text-indigo-600 transition active:scale-95 shadow-sm';
        btn.innerText = opt;
        btn.onclick = () => {
            if (question.type === 'region-selector' && !selectedRegion && opt !== "北海道") {
                selectedRegion = opt;
                renderOptions();
            } else if (question.type === 'region-selector' && !selectedPref) {
                selectedPref = opt;
                renderOptions();
            } else {
                handleAnswer(opt);
            }
        };
        grid.appendChild(btn);
    });

    // Update Counter & Dots
    document.getElementById('step-counter').innerText = `Question ${currentStep + 1} / ${MATCHING_QUESTIONS.length}`;
    const dots = document.getElementById('step-dots');
    dots.innerHTML = '';
    for(let i=0; i<MATCHING_QUESTIONS.length; i++) {
        const dot = document.createElement('div');
        dot.className = `w-1.5 h-1.5 rounded-full transition-all ${i <= currentStep ? 'bg-indigo-600 scale-125' : 'bg-gray-200'}`;
        dots.appendChild(dot);
    }
}

function handleAnswer(answer) {
    const q = MATCHING_QUESTIONS[currentStep];
    
    if (q.id === 'area') {
        answers['pref'] = answer;
        answers['muni'] = [...selectedCities];
        const display = selectedCities.length > 0 ? `${answer} (${selectedCities.length}エリア)` : answer;
        addChatMessage(display, 'user');
    } else {
        answers[q.id] = answer;
        addChatMessage(answer, 'user');
    }

    if (currentStep < MATCHING_QUESTIONS.length - 1) {
        currentStep++;
        selectedRegion = null;
        selectedPref = null;
        setTimeout(() => {
            addChatMessage(MATCHING_QUESTIONS[currentStep].question, 'bot');
            renderOptions();
        }, 500);
    } else {
        finishDiagnosis();
    }
}

function addChatMessage(text, type) {
    const history = document.getElementById('chat-history');
    const msgDiv = document.createElement('div');
    msgDiv.className = `flex ${type === 'bot' ? 'justify-start' : 'justify-end'} animate-fade-in`;
    
    const content = document.createElement('div');
    content.className = `max-w-[85%] px-5 py-3 rounded-2xl text-sm font-black shadow-sm leading-relaxed ${
        type === 'bot' 
        ? 'bg-white text-gray-800 border border-indigo-50 rounded-bl-none' 
        : 'bg-indigo-600 text-white rounded-br-none'
    }`;
    content.innerText = text;
    
    msgDiv.appendChild(content);
    history.appendChild(msgDiv);
    
    // Auto Scroll
    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
}

async function finishDiagnosis() {
    document.getElementById('options-grid').innerHTML = '';
    setTimeout(() => {
        addChatMessage("ありがとうございます！あなたにぴったりの求人を解析しました...", 'bot');
    }, 500);

    setTimeout(() => {
        document.getElementById('chat-history').classList.add('hidden');
        document.querySelector('.fixed.bottom-0').classList.add('hidden');
        document.getElementById('matcher-results').classList.remove('hidden');
        
        loadMatchedJobs();
    }, 1500);
}

function loadMatchedJobs() {
    const list = document.getElementById('matched-jobs-list');
    list.innerHTML = '<div class="text-center p-10 font-bold text-slate-400">求人を読み込んでいます...</div>';

    const params = new URLSearchParams(answers);
    if (answers.muni && answers.muni.length > 0) {
        params.delete('muni'); // delete serialized version
        answers.muni.forEach(m => params.append('muni[]', m));
    }

    fetch(`<?php echo admin_url('admin-ajax.php'); ?>?action=get_matched_jobs&` + params.toString())
        .then(res => res.json())
        .then(data => {
            list.innerHTML = '';
            if (data.length === 0) {
                list.innerHTML = '<div class="text-center p-10 font-bold text-slate-400">マッチする求人が見つかりませんでした。別の条件で再診断してみてください。</div>';
                return;
            }
            data.forEach(job => {
                list.innerHTML += `
                    <a href="${job.link}" class="block bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 group text-left">
                        <div class="flex items-center gap-6">
                            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-slate-100 shrink-0 border border-indigo-50">
                                <img src="${job.thumbnail}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-grow">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black rounded uppercase tracking-widest mb-2 inline-block">${job.category}</span>
                                <h4 class="text-lg font-black text-slate-900 group-hover:text-indigo-600 transition mb-1">${job.title}</h4>
                                <div class="flex items-center gap-3 text-slate-400 text-xs font-bold">
                                    <span><i class="fas fa-money-bill-wave mr-1"></i>${job.salary}</span>
                                    <span><i class="fas fa-map-marker-alt mr-1"></i>${job.area}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                `;
            });
        });
}

function restartDiagnosis() {
    currentStep = 0;
    answers = {};
    selectedRegion = null;
    selectedPref = null;
    selectedCities = [];
    document.getElementById('chat-history').innerHTML = `
        <div class="flex justify-start animate-fade-in">
            <div class="max-w-[85%] px-5 py-3 rounded-2xl text-sm font-black shadow-sm leading-relaxed bg-white text-gray-800 border border-indigo-50 rounded-bl-none">
                こんにちは！あなたの希望に合わせて、30秒でぴったりの仕事を提案します。
            </div>
        </div>
        <div class="flex justify-start animate-fade-in">
            <div class="max-w-[85%] px-5 py-3 rounded-2xl text-sm font-black shadow-sm leading-relaxed bg-white text-gray-800 border border-indigo-50 rounded-bl-none">
                まずは、希望の勤務エリアを教えてください！
            </div>
        </div>
    `;
    document.getElementById('chat-history').classList.remove('hidden');
    document.querySelector('.fixed.bottom-0').classList.remove('hidden');
    document.getElementById('matcher-results').classList.add('hidden');
    renderOptions();
}

renderOptions();
</script>

<?php get_footer(); ?>
