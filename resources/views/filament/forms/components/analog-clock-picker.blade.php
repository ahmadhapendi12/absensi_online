<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{
            state: $wire.entangle('{{ $getStatePath() }}'),
            open: false,
            mode: 'hour', // 'hour' | 'minute'
            period: 'PM', // 'AM' | 'PM'
            hour: 17,
            minute: 0,
            isDragging: false,

            init() {
                this.parseInitialState();
                this.$watch('state', () => this.parseInitialState());
            },

            parseInitialState() {
                if (this.state && typeof this.state === 'string' && this.state.includes(':')) {
                    const parts = this.state.split(':');
                    let h = parseInt(parts[0], 10) || 0;
                    this.minute = parseInt(parts[1], 10) || 0;
                    if (h >= 12) {
                        this.period = 'PM';
                        this.hour = h;
                    } else {
                        this.period = 'AM';
                        this.hour = h;
                    }
                } else if (!this.state) {
                    this.hour = 17;
                    this.minute = 0;
                    this.period = 'PM';
                    this.syncState();
                }
            },

            syncState() {
                let hStr = String(this.hour).padStart(2, '0');
                let mStr = String(this.minute).padStart(2, '0');
                this.state = `${hStr}:${mStr}`;
            },

            get formattedDisplay() {
                let hStr = String(this.hour).padStart(2, '0');
                let mStr = String(this.minute).padStart(2, '0');
                return `${hStr}:${mStr} WIB`;
            },

            get displayHour() {
                return String(this.hour).padStart(2, '0');
            },

            get displayMinute() {
                return String(this.minute).padStart(2, '0');
            },

            get handAngle() {
                if (this.mode === 'hour') {
                    return (this.hour % 12) * 30;
                } else {
                    return this.minute * 6;
                }
            },

            getHourItems() {
                let base = this.period === 'AM' ? 0 : 12;
                let items = [];
                // Angles 1 to 12
                for (let i = 1; i <= 12; i++) {
                    let val = i === 12 ? (this.period === 'AM' ? 0 : 12) : (base + i);
                    let angle = i * 30;
                    let rad = (angle - 90) * (Math.PI / 180);
                    let r = 85; // radius in px
                    let x = 110 + r * Math.cos(rad);
                    let y = 110 + r * Math.sin(rad);
                    items.push({
                        val: val,
                        label: String(val).padStart(2, '0'),
                        angle: angle,
                        x: x,
                        y: y,
                        isSelected: this.hour === val
                    });
                }
                return items;
            },

            getMinuteItems() {
                let items = [];
                for (let i = 0; i < 60; i += 5) {
                    let angle = i * 6;
                    let rad = (angle - 90) * (Math.PI / 180);
                    let r = 85;
                    let x = 110 + r * Math.cos(rad);
                    let y = 110 + r * Math.sin(rad);
                    items.push({
                        val: i,
                        label: String(i).padStart(2, '0'),
                        angle: angle,
                        x: x,
                        y: y,
                        isSelected: Math.abs(this.minute - i) < 3
                    });
                }
                return items;
            },

            selectHour(val) {
                this.hour = val;
                this.syncState();
                setTimeout(() => {
                    this.mode = 'minute';
                }, 250);
            },

            selectMinute(val) {
                this.minute = val;
                this.syncState();
            },

            setPeriod(p) {
                if (this.period === p) return;
                this.period = p;
                if (p === 'PM' && this.hour < 12) {
                    this.hour += 12;
                } else if (p === 'AM' && this.hour >= 12) {
                    this.hour -= 12;
                }
                this.syncState();
            },

            handleDialClick(e) {
                const rect = this.$refs.dial.getBoundingClientRect();
                const cx = rect.left + rect.width / 2;
                const cy = rect.top + rect.height / 2;
                let angle = Math.atan2(e.clientY - cy, e.clientX - cx) * (180 / Math.PI) + 90;
                if (angle < 0) angle += 360;

                if (this.mode === 'hour') {
                    let step = Math.round(angle / 30);
                    let clockNum = step === 0 ? 12 : step;
                    let val = this.period === 'AM' 
                        ? (clockNum === 12 ? 0 : clockNum) 
                        : (clockNum === 12 ? 12 : clockNum + 12);
                    this.selectHour(val);
                } else {
                    let min = Math.round(angle / 6) % 60;
                    this.selectMinute(min);
                }
            }
        }"
        class="relative"
    >
        <!-- Trigger Display Button -->
        <button
            type="button"
            @click="open = !open; mode = 'hour'"
            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm hover:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
        >
            <div class="flex items-center space-x-2.5">
                <div class="w-7 h-7 rounded-full bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 flex items-center justify-center text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2"/>
                    </svg>
                </div>
                <span class="font-bold text-sm tracking-wide" x-text="formattedDisplay"></span>
            </div>
            <span class="text-xs text-primary-600 font-semibold bg-primary-50 dark:bg-primary-950/60 px-2.5 py-1 rounded-full border border-primary-200 dark:border-primary-800">
                Putar Jarum Jam
            </span>
        </button>

        <!-- Analog Clock Modal / Dropdown -->
        <div
            x-show="open"
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            style="display: none;"
            class="absolute z-50 mt-2 left-0 w-80 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 p-5"
        >
            <!-- Clock Header: Digital Time & AM/PM Toggle -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800 mb-4">
                <div class="flex items-center space-x-1.5">
                    <button
                        type="button"
                        @click="mode = 'hour'"
                        :class="mode === 'hour' ? 'bg-primary-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200'"
                        class="px-3.5 py-2 rounded-xl text-2xl font-black font-mono transition-all"
                        x-text="displayHour"
                    ></button>
                    <span class="text-2xl font-bold text-gray-400 animate-pulse">:</span>
                    <button
                        type="button"
                        @click="mode = 'minute'"
                        :class="mode === 'minute' ? 'bg-primary-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200'"
                        class="px-3.5 py-2 rounded-xl text-2xl font-black font-mono transition-all"
                        x-text="displayMinute"
                    ></button>
                </div>

                <div class="flex flex-col space-y-1">
                    <button
                        type="button"
                        @click="setPeriod('AM')"
                        :class="period === 'AM' ? 'bg-primary-600 text-white font-bold' : 'bg-gray-100 dark:bg-gray-800 text-gray-500'"
                        class="px-2.5 py-1 text-xs rounded-lg transition"
                    >Pagi (AM)</button>
                    <button
                        type="button"
                        @click="setPeriod('PM')"
                        :class="period === 'PM' ? 'bg-primary-600 text-white font-bold' : 'bg-gray-100 dark:bg-gray-800 text-gray-500'"
                        class="px-2.5 py-1 text-xs rounded-lg transition"
                    >Sore/Malam (PM)</button>
                </div>
            </div>

            <!-- Mode Description -->
            <div class="text-center mb-3">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                    <span x-show="mode === 'hour'">Pilih / Putar <strong class="text-primary-600">Jarum Jam</strong> (0-23)</span>
                    <span x-show="mode === 'minute'">Pilih / Putar <strong class="text-primary-600">Jarum Menit</strong> (00-59)</span>
                </span>
            </div>

            <!-- Analog Clock Face -->
            <div class="flex justify-center mb-4">
                <div
                    x-ref="dial"
                    @click="handleDialClick($event)"
                    class="relative w-[220px] h-[220px] rounded-full bg-gray-50 dark:bg-gray-800 border-4 border-gray-200 dark:border-gray-700 cursor-pointer select-none shadow-inner flex items-center justify-center transition-colors"
                >
                    <!-- Center Pivot -->
                    <div class="absolute w-3 h-3 bg-primary-600 rounded-full z-20 shadow"></div>

                    <!-- Rotating Hand Pointer -->
                    <div
                        class="absolute bottom-1/2 left-1/2 origin-bottom z-10 pointer-events-none transition-transform duration-200 ease-out"
                        :style="`transform: translateX(-50%) rotate(${handAngle}deg); height: 85px;`"
                    >
                        <!-- Pointer Line -->
                        <div class="w-1 h-full bg-primary-600 mx-auto rounded-full"></div>
                        <!-- Pointer Circle Bulb at End -->
                        <div class="absolute -top-3.5 left-1/2 transform -translate-x-1/2 w-8 h-8 rounded-full bg-primary-600 opacity-90 border-2 border-white shadow-lg"></div>
                    </div>

                    <!-- Numbers around dial for HOURS -->
                    <template x-if="mode === 'hour'">
                        <div>
                            <template x-for="item in getHourItems()" :key="item.val">
                                <div
                                    @click.stop="selectHour(item.val)"
                                    class="absolute w-8 h-8 -ml-4 -mt-4 flex items-center justify-center rounded-full text-xs font-bold transition-all z-20"
                                    :class="item.isSelected ? 'text-white font-black scale-110' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                                    :style="`left: ${item.x}px; top: ${item.y}px;`"
                                    x-text="item.label"
                                ></div>
                            </template>
                        </div>
                    </template>

                    <!-- Numbers around dial for MINUTES -->
                    <template x-if="mode === 'minute'">
                        <div>
                            <template x-for="item in getMinuteItems()" :key="item.val">
                                <div
                                    @click.stop="selectMinute(item.val)"
                                    class="absolute w-8 h-8 -ml-4 -mt-4 flex items-center justify-center rounded-full text-xs font-bold transition-all z-20"
                                    :class="item.isSelected ? 'text-white font-black scale-110' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                                    :style="`left: ${item.x}px; top: ${item.y}px;`"
                                    x-text="item.label"
                                ></div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800">
                <button
                    type="button"
                    @click="mode = (mode === 'hour' ? 'minute' : 'hour')"
                    class="text-xs text-primary-600 dark:text-primary-400 font-semibold hover:underline"
                    x-text="mode === 'hour' ? 'Ke Jarum Menit →' : '← Ke Jarum Jam'"
                ></button>
                <button
                    type="button"
                    @click="syncState(); open = false"
                    class="bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-md transition"
                >
                    Selesai
                </button>
            </div>
        </div>
    </div>
</x-dynamic-component>
