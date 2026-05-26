<script setup>
import { ref } from 'vue';

const errorMessage = ref('');
const loading = ref(false);
const shortenedUrl = ref('');
const urlInput = ref('');
const copied = ref(false);

function handleShorten() {
    errorMessage.value = '';
    shortenedUrl.value = '';

    if (!urlInput.value.trim()) {
        errorMessage.value = 'enter a URL first.';
        return;
    }

    const raw = urlInput.value.trim();
    const fullUrl = /^https?:\/\//i.test(raw) ? raw : 'https://' + raw;

    loading.value = true;
    axios.post('/api/v1/shorten', new URLSearchParams({ url: fullUrl }))
        .then(response => {
            if (response.data?.shortenedUrl) {
                shortenedUrl.value = response.data.shortenedUrl;
            } else {
                errorMessage.value = 'something went wrong.';
            }
        })
        .catch(error => {
            const data = error.response?.data;
            errorMessage.value = data?.error
                ?? data?.errors?.url?.[0]
                ?? data?.message
                ?? 'an error occurred.';
        })
        .finally(() => {
            loading.value = false;
        });
}

function copyUrl() {
    navigator.clipboard.writeText(shortenedUrl.value).then(() => {
        copied.value = true;
        setTimeout(() => { copied.value = false; }, 2000);
    });
}
</script>

<template>
    <div class="page">

        <!-- Grain texture overlay -->
        <div class="grain" aria-hidden="true"></div>

        <!-- Ghosted background word -->
        <div class="ghost-text" aria-hidden="true">SHORTEN</div>

        <main class="main">

            <!-- Top label -->
            <p class="eyebrow">// URL.CUT &mdash; v1.0</p>

            <!-- Heading -->
            <h1 class="heading">
                <span class="heading-line">MAKE IT</span>
                <span class="heading-line red">SHORT.</span>
            </h1>

            <!-- Card -->
            <div class="card" :class="{ 'card--has-result': shortenedUrl }">

                <!-- Input row -->
                <div class="input-row">
                    <span class="prefix">https://</span>
                    <input
                        v-model="urlInput"
                        @keydown.enter="handleShorten"
                        type="text"
                        class="url-input"
                        placeholder="your-long-url.com/goes/here"
                        spellcheck="false"
                        autocomplete="off"
                        autocorrect="off"
                    />
                    <button
                        @click="handleShorten"
                        class="cut-btn"
                        :disabled="loading"
                    >
                        <span v-if="!loading">CUT&nbsp;→</span>
                        <span v-else class="spin">◌</span>
                    </button>
                </div>

                <!-- Error -->
                <p v-if="errorMessage" class="error-msg">
                    <span class="error-icon">✕</span> {{ errorMessage }}
                </p>

                <!-- Result -->
                <Transition name="reveal">
                    <div v-if="shortenedUrl" class="result">
                        <div class="result-header">
                            <span class="result-label">YOUR SHORT LINK</span>
                            <div class="result-line"></div>
                        </div>
                        <div class="result-row">
                            <a :href="shortenedUrl" target="_blank" rel="noopener" class="result-url">
                                {{ shortenedUrl }}
                            </a>
                            <button
                                @click="copyUrl"
                                class="copy-btn"
                                :class="{ 'copy-btn--done': copied }"
                            >
                                {{ copied ? 'COPIED ✓' : 'COPY' }}
                            </button>
                        </div>
                    </div>
                </Transition>

            </div>

            <!-- Footer -->
            <p class="footer">SNEV.DEV &mdash; {{ new Date().getFullYear() }}</p>

        </main>
    </div>
</template>

<style>
/* ─── Variables ──────────────────────────────────── */
:root {
    --bg:         #0C0C0C;
    --surface:    #131313;
    --border:     #222222;
    --text:       #EBEBEB;
    --muted:      #777777;
    --red:        #FF2800;
    --yellow:     #E8FF00;
}

/* ─── Page shell ─────────────────────────────────── */
.page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 1.5rem;
    position: relative;
    overflow: hidden;
    background: var(--bg);
    font-family: 'IBM Plex Mono', monospace;
}

/* ─── Grain ──────────────────────────────────────── */
.grain {
    position: fixed;
    inset: -200%;
    width: 400%;
    height: 400%;
    pointer-events: none;
    z-index: 50;
    opacity: 0.028;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    background-size: 256px 256px;
    animation: grain-shift 8s steps(1) infinite;
}

@keyframes grain-shift {
    0%  { transform: translate(0, 0); }
    10% { transform: translate(-5%, -10%); }
    20% { transform: translate(-15%, 5%); }
    30% { transform: translate(7%, -25%); }
    40% { transform: translate(-5%, 25%); }
    50% { transform: translate(-15%, 10%); }
    60% { transform: translate(15%, 0%); }
    70% { transform: translate(0%, 15%); }
    80% { transform: translate(3%, 35%); }
    90% { transform: translate(-10%, 10%); }
}

/* ─── Ghost background text ──────────────────────── */
.ghost-text {
    position: fixed;
    bottom: -0.12em;
    left: 50%;
    transform: translateX(-50%);
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(100px, 22vw, 300px);
    color: transparent;
    -webkit-text-stroke: 1px rgba(255, 255, 255, 0.18);
    white-space: nowrap;
    pointer-events: none;
    user-select: none;
    letter-spacing: 0.06em;
    animation: ghost-drift 20s ease-in-out infinite alternate;
}

@keyframes ghost-drift {
    from { opacity: 0.6; }
    to   { opacity: 1; }
}

/* ─── Main layout ────────────────────────────────── */
.main {
    width: 100%;
    max-width: 660px;
    position: relative;
    z-index: 1;
    animation: page-in 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@keyframes page-in {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ─── Eyebrow ─────────────────────────────────────── */
.eyebrow {
    font-size: 11px;
    letter-spacing: 0.18em;
    color: var(--muted);
    margin-bottom: 2rem;
    animation: page-in 0.6s 0.05s cubic-bezier(0.16, 1, 0.3, 1) both;
}

/* ─── Heading ─────────────────────────────────────── */
.heading {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(80px, 15vw, 144px);
    line-height: 0.88;
    letter-spacing: 0.01em;
    margin-bottom: 2.5rem;
    display: flex;
    flex-direction: column;
}

.heading-line {
    display: block;
    animation: page-in 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
}

.heading-line:nth-child(1) { animation-delay: 0.08s; }
.heading-line:nth-child(2) { animation-delay: 0.14s; }

.heading-line.red {
    color: var(--red);
    /* Subtle texture on the red text */
    text-shadow: 0 0 80px rgba(255, 40, 0, 0.25);
}

/* ─── Card ────────────────────────────────────────── */
.card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-left: 3px solid var(--red);
    padding: 1.75rem 2rem;
    animation: page-in 0.6s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
    transition: border-left-color 0.3s;
}

.card--has-result {
    border-left-color: var(--yellow);
}

/* ─── Input row ───────────────────────────────────── */
.input-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.prefix {
    font-size: 12px;
    color: var(--muted);
    letter-spacing: 0.05em;
    flex-shrink: 0;
    user-select: none;
}

.url-input {
    flex: 1;
    min-width: 0;
    background: transparent;
    border: none;
    border-bottom: 1px solid var(--border);
    outline: none;
    color: var(--text);
    font-family: 'IBM Plex Mono', monospace;
    font-size: 14px;
    padding: 0.3rem 0.5rem 0.4rem;
    letter-spacing: 0.02em;
    transition: border-color 0.2s;
}

.url-input:focus {
    border-bottom-color: var(--muted);
}

.url-input::placeholder {
    color: #484848;
}

.cut-btn {
    flex-shrink: 0;
    background: var(--red);
    color: #fff;
    border: none;
    cursor: pointer;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 17px;
    letter-spacing: 0.12em;
    padding: 0.55rem 1.4rem 0.45rem;
    transition: background 0.15s, transform 0.08s;
    outline: none;
    user-select: none;
}

.cut-btn:hover:not(:disabled) {
    background: #FF4A1C;
}

.cut-btn:active:not(:disabled) {
    transform: scale(0.96);
}

.cut-btn:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

.spin {
    display: inline-block;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}

/* ─── Error ───────────────────────────────────────── */
.error-msg {
    font-size: 12px;
    color: var(--red);
    letter-spacing: 0.06em;
    margin-top: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.error-icon {
    font-size: 10px;
    opacity: 0.8;
}

/* ─── Result ──────────────────────────────────────── */
.result {
    margin-top: 1.75rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border);
}

.result-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.85rem;
}

.result-label {
    font-size: 9px;
    letter-spacing: 0.25em;
    color: var(--muted);
    white-space: nowrap;
}

.result-line {
    flex: 1;
    height: 1px;
    background: var(--border);
}

.result-row {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.result-url {
    flex: 1;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 15px;
    font-weight: 500;
    color: var(--yellow);
    text-decoration: none;
    letter-spacing: 0.02em;
    word-break: break-all;
    padding-bottom: 2px;
    border-bottom: 1px solid rgba(232, 255, 0, 0.3);
    transition: opacity 0.2s, border-color 0.2s;
}

.result-url:hover {
    opacity: 0.75;
    border-bottom-color: rgba(232, 255, 0, 0.6);
}

.copy-btn {
    flex-shrink: 0;
    background: transparent;
    border: 1px solid var(--border);
    color: var(--muted);
    font-family: 'IBM Plex Mono', monospace;
    font-size: 10px;
    letter-spacing: 0.14em;
    padding: 0.4rem 0.9rem;
    cursor: pointer;
    transition: all 0.15s;
    outline: none;
    white-space: nowrap;
}

.copy-btn:hover {
    border-color: var(--text);
    color: var(--text);
}

.copy-btn--done {
    border-color: var(--yellow) !important;
    color: var(--yellow) !important;
}

/* ─── Footer ──────────────────────────────────────── */
.footer {
    font-size: 10px;
    letter-spacing: 0.18em;
    color: #505050;
    margin-top: 1.5rem;
    text-align: right;
    animation: page-in 0.6s 0.25s cubic-bezier(0.16, 1, 0.3, 1) both;
}

/* ─── Reveal transition ───────────────────────────── */
.reveal-enter-active {
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.reveal-enter-from {
    opacity: 0;
    transform: translateY(10px);
}

/* ─── Responsive ──────────────────────────────────── */
@media (max-width: 500px) {
    .prefix { display: none; }

    .input-row { flex-wrap: wrap; }

    .cut-btn {
        width: 100%;
        margin-top: 0.5rem;
        text-align: center;
    }

    .card { padding: 1.25rem; }
}
</style>
