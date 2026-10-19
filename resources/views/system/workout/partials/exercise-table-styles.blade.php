{{-- Workout builder: themed exercise table (dark default + light override via [data-bs-theme]). --}}
<style>
    /* ------------------------------------------------------------------
       Design tokens â€” dark (default, matches gym-polish slate theme)
       ------------------------------------------------------------------ */
    .wb-scope {
        --wb-surface: linear-gradient(180deg, #0F172A 0%, #111B2E 100%);
        --wb-surface-solid: #0F172A;
        --wb-row-bg: rgba(30, 41, 59, .55);
        --wb-row-hover: rgba(30, 41, 59, .9);
        --wb-row-border: rgba(51, 65, 85, .6);
        --wb-head-bg: rgba(15, 23, 42, .85);
        --wb-head-text: #94A3B8;
        --wb-text: #F1F5F9;
        --wb-text-muted: #94A3B8;
        --wb-input-bg: rgba(2, 6, 23, .55);
        --wb-input-border: #334155;
        --wb-input-border-hover: #475569;
        --wb-accent: var(--gp-accent, #FACC15);
        --wb-accent-soft: rgba(250, 204, 21, .14);
        --wb-ring: rgba(250, 204, 21, .28);
        --wb-danger: #F43F5E;
        --wb-danger-soft: rgba(244, 63, 94, .14);
        --wb-shadow: 0 10px 30px -12px rgba(0, 0, 0, .6);
    }
    [data-bs-theme="light"] .wb-scope {
        --wb-surface: linear-gradient(180deg, #F1F5F9 0%, #EEF2F7 100%);
        --wb-surface-solid: #F1F5F9;
        --wb-row-bg: #F8FAFC;
        --wb-row-hover: #E9EEF5;
        --wb-row-border: #DCE3EC;
        --wb-head-bg: #E2E8F0;
        --wb-head-text: #475569;
        --wb-text: #0F172A;
        --wb-text-muted: #64748B;
        --wb-input-bg: #FDFEFF;
        --wb-input-border: #CBD5E1;
        --wb-input-border-hover: #94A3B8;
        --wb-accent: var(--gp-accent, #D97706);
        --wb-accent-soft: rgba(217, 119, 6, .12);
        --wb-ring: rgba(217, 119, 6, .25);
        --wb-shadow: 0 8px 24px -14px rgba(15, 23, 42, .25);
    }

    /* ------------------------------------------------------------------
       Day step layout
       ------------------------------------------------------------------ */
    .wb-day__header {
        display: flex;
        align-items: center;
        gap: .85rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1.1rem;
        border-bottom: 1px solid var(--wb-row-border);
    }
    .wb-day__badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        height: 44px;
        padding: 0 .75rem;
        border-radius: 12px;
        background: var(--wb-accent-soft);
        color: var(--wb-accent);
        font-weight: 800;
        font-size: 1.05rem;
        letter-spacing: .02em;
    }
    .wb-day__title {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--wb-text);
        letter-spacing: -.01em;
    }
    .wb-day__subtitle {
        margin: .15rem 0 0;
        font-size: .85rem;
        color: var(--wb-text-muted);
    }
    .wb-field-label {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
    }
    .wb-field-label i { font-size: .9rem; }

    /* ------------------------------------------------------------------
       Exercises card
       ------------------------------------------------------------------ */
    .wb-exercises {
        background: var(--wb-surface);
        border: 1px solid var(--wb-row-border);
        border-radius: 16px;
        box-shadow: var(--wb-shadow);
        margin: 1.75rem 0;
        overflow: hidden;
    }
    .wb-exercises__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid var(--wb-row-border);
    }
    .wb-exercises__title {
        display: flex;
        align-items: center;
        gap: .8rem;
    }
    .wb-exercises__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 11px;
        background: var(--wb-accent-soft);
        color: var(--wb-accent);
        font-size: 1rem;
    }
    .wb-exercises__title h3 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--wb-text);
    }
    .wb-exercises__title p {
        margin: .1rem 0 0;
        font-size: .8rem;
        color: var(--wb-text-muted);
    }
    .wb-count {
        flex: 0 0 auto;
        padding: .35rem .75rem;
        border-radius: 999px;
        border: 1px solid var(--wb-row-border);
        color: var(--wb-text-muted);
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
    }
    .wb-count__num { color: var(--wb-accent); }

    /* ------------------------------------------------------------------
       Warmup & Post-Workout Routine Sections â€” shared lavender identity
       (#A78BFA). The violet gradient lives on ::before so it can fade
       smoothly to a neutral slate on hover (gradients can't be
       transitioned directly). Main Exercises card is NOT affected.
       ------------------------------------------------------------------ */
    .wb-exercises--warmup,
    .wb-exercises--post {
        --wb-rt-accent: #A78BFA;
        --wb-rt-accent-rgb: 167, 139, 250;
        --wb-rt-base: rgba(15, 23, 42, 0.8);
        --wb-rt-hover: rgba(30, 41, 59, 0.8);
        --wb-rt-title: #DDD6FE;
        --wb-rt-sub: #CBD5E1;
        position: relative;
        isolation: isolate;
        background: var(--wb-rt-base) !important;
        border: 1px solid rgba(var(--wb-rt-accent-rgb), 0.3) !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 10px 28px -12px rgba(0, 0, 0, 0.55) !important;
        transition: background-color .3s ease-in-out, border-color .3s ease-in-out, box-shadow .3s ease-in-out !important;
    }
    .wb-exercises--warmup::before,
    .wb-exercises--post::before {
        content: "";
        position: absolute;
        inset: 0;
        z-index: -1;
        border-radius: inherit;
        pointer-events: none;
        background: linear-gradient(135deg,
            rgba(var(--wb-rt-accent-rgb), 0.10) 0%,
            rgba(15, 23, 42, 0.60) 50%,
            rgba(15, 23, 42, 0.80) 100%);
        opacity: 1;
        transition: opacity .3s ease-in-out;
    }
    .wb-exercises--warmup:hover,
    .wb-exercises--post:hover {
        background: var(--wb-rt-hover) !important;
        border-color: rgba(71, 85, 105, 0.5) !important;
    }
    .wb-exercises--warmup:hover::before,
    .wb-exercises--post:hover::before {
        opacity: 0;
    }

    /* Header */
    .wb-exercises--warmup .wb-exercises__header,
    .wb-exercises--post .wb-exercises__header {
        background: transparent !important;
        border-bottom: 1px solid rgba(var(--wb-rt-accent-rgb), 0.18) !important;
        transition: border-color .3s ease-in-out;
    }
    .wb-exercises--warmup:hover .wb-exercises__header,
    .wb-exercises--post:hover .wb-exercises__header {
        border-bottom-color: rgba(71, 85, 105, 0.45) !important;
    }
    .wb-exercises--warmup .wb-exercises__icon,
    .wb-exercises--post .wb-exercises__icon {
        background: rgba(var(--wb-rt-accent-rgb), 0.15) !important;
        color: var(--wb-rt-accent) !important;
        border: 1px solid rgba(var(--wb-rt-accent-rgb), 0.25) !important;
    }
    .wb-exercises--warmup .wb-exercises__title h3,
    .wb-exercises--post .wb-exercises__title h3 {
        color: var(--wb-rt-title) !important;
        font-weight: 800 !important;
    }
    .wb-exercises--warmup .wb-exercises__title p,
    .wb-exercises--post .wb-exercises__title p {
        color: var(--wb-rt-sub) !important;
    }

    /* Count pill */
    .wb-exercises--warmup .wb-count,
    .wb-exercises--post .wb-count {
        background: rgba(var(--wb-rt-accent-rgb), 0.15) !important;
        border: 1px solid rgba(var(--wb-rt-accent-rgb), 0.25) !important;
        color: var(--wb-rt-accent) !important;
    }
    .wb-exercises--warmup .wb-count__num,
    .wb-exercises--post .wb-count__num {
        color: var(--wb-rt-accent) !important;
        font-weight: 800 !important;
    }

    /* Table chrome â€” inputs keep the shared high-contrast tokens */
    .wb-exercises--warmup .wb-table thead th,
    .wb-exercises--post .wb-table thead th {
        background: rgba(15, 23, 42, 0.7) !important;
        color: #C4B5FD !important;
        border-bottom-color: rgba(var(--wb-rt-accent-rgb), 0.18) !important;
    }
    .wb-exercises--warmup .wb-th-hint,
    .wb-exercises--post .wb-th-hint {
        background: rgba(var(--wb-rt-accent-rgb), 0.18) !important;
        color: #EDE9FE !important;
    }
    .wb-exercises--warmup .wb-table tbody.exercise-row td,
    .wb-exercises--post .wb-table tbody.exercise-row td {
        background: rgba(15, 23, 42, 0.55);
        border-color: rgba(var(--wb-rt-accent-rgb), 0.14) !important;
    }
    .wb-exercises--warmup .wb-table tbody.exercise-row td.wb-cell-idx,
    .wb-exercises--post .wb-table tbody.exercise-row td.wb-cell-idx {
        border-inline-start-color: var(--wb-rt-accent) !important;
    }
    .wb-exercises--warmup .wb-table tbody.exercise-row:hover td,
    .wb-exercises--post .wb-table tbody.exercise-row:hover td {
        background: rgba(51, 65, 85, 0.6) !important;
    }

    /* Add button */
    .wb-exercises--warmup .wb-add,
    .wb-exercises--post .wb-add {
        border: 1.5px dashed rgba(var(--wb-rt-accent-rgb), 0.35) !important;
        color: var(--wb-rt-accent) !important;
        background: rgba(var(--wb-rt-accent-rgb), 0.05) !important;
    }
    .wb-exercises--warmup .wb-add:hover,
    .wb-exercises--warmup .wb-add:focus-visible,
    .wb-exercises--post .wb-add:hover,
    .wb-exercises--post .wb-add:focus-visible {
        border-color: var(--wb-rt-accent) !important;
        background: rgba(var(--wb-rt-accent-rgb), 0.15) !important;
        color: #ffffff !important;
    }

    /* Light theme */
    [data-bs-theme="light"] .wb-exercises--warmup,
    [data-bs-theme="light"] .wb-exercises--post {
        --wb-rt-base: rgba(241, 245, 249, 0.9);
        --wb-rt-hover: rgba(226, 232, 240, 0.7);
        --wb-rt-title: #5B21B6;
        --wb-rt-sub: #475569;
        box-shadow: 0 8px 22px -14px rgba(15, 23, 42, 0.25) !important;
    }
    [data-bs-theme="light"] .wb-exercises--warmup::before,
    [data-bs-theme="light"] .wb-exercises--post::before {
        background: linear-gradient(135deg,
            rgba(var(--wb-rt-accent-rgb), 0.15) 0%,
            rgba(241, 245, 249, 0.70) 50%,
            rgba(241, 245, 249, 0.90) 100%);
    }
    [data-bs-theme="light"] .wb-exercises--warmup:hover,
    [data-bs-theme="light"] .wb-exercises--post:hover {
        border-color: #CBD5E1 !important;
    }
    [data-bs-theme="light"] .wb-exercises--warmup:hover .wb-exercises__header,
    [data-bs-theme="light"] .wb-exercises--post:hover .wb-exercises__header {
        border-bottom-color: #CBD5E1 !important;
    }
    [data-bs-theme="light"] .wb-exercises--warmup .wb-exercises__icon,
    [data-bs-theme="light"] .wb-exercises--post .wb-exercises__icon,
    [data-bs-theme="light"] .wb-exercises--warmup .wb-count,
    [data-bs-theme="light"] .wb-exercises--post .wb-count,
    [data-bs-theme="light"] .wb-exercises--warmup .wb-count__num,
    [data-bs-theme="light"] .wb-exercises--post .wb-count__num,
    [data-bs-theme="light"] .wb-exercises--warmup .wb-add,
    [data-bs-theme="light"] .wb-exercises--post .wb-add {
        color: #7C3AED !important;
    }
    [data-bs-theme="light"] .wb-exercises--warmup .wb-add:hover,
    [data-bs-theme="light"] .wb-exercises--post .wb-add:hover {
        color: #5B21B6 !important;
    }
    [data-bs-theme="light"] .wb-exercises--warmup .wb-table thead th,
    [data-bs-theme="light"] .wb-exercises--post .wb-table thead th {
        background: rgba(226, 232, 240, 0.9) !important;
        color: #5B21B6 !important;
    }
    [data-bs-theme="light"] .wb-exercises--warmup .wb-th-hint,
    [data-bs-theme="light"] .wb-exercises--post .wb-th-hint {
        color: #5B21B6 !important;
    }
    [data-bs-theme="light"] .wb-exercises--warmup .wb-table tbody.exercise-row td,
    [data-bs-theme="light"] .wb-exercises--post .wb-table tbody.exercise-row td {
        background: rgba(255, 255, 255, 0.75);
    }
    [data-bs-theme="light"] .wb-exercises--warmup .wb-table tbody.exercise-row:hover td,
    [data-bs-theme="light"] .wb-exercises--post .wb-table tbody.exercise-row:hover td {
        background: rgba(241, 245, 249, 0.95) !important;
    }

    @media (prefers-reduced-motion: reduce) {
        .wb-exercises--warmup, .wb-exercises--post,
        .wb-exercises--warmup::before, .wb-exercises--post::before { transition: none !important; }
    }

    .wb-table-wrap {
        padding: .5rem .75rem .25rem;
        overflow-x: auto;
    }

    /* ------------------------------------------------------------------
       Table
       ------------------------------------------------------------------ */
    .wb-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 .6rem;
        table-layout: fixed;
        counter-reset: wb-exercise;
        color: var(--wb-text);
    }
    .wb-col-idx     { width: 44px; }
    .wb-col-name    { width: auto; }
    .wb-col-num     { width: 74px; }
    .wb-col-tempo   { width: 88px; }
    .wb-col-weights { width: 214px; }
    .wb-col-action  { width: 52px; }

    .wb-table thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        padding: .7rem .5rem;
        background: var(--wb-head-bg);
        color: var(--wb-head-text);
        font-size: .7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .09em;
        white-space: nowrap;
        border-bottom: 2px solid var(--wb-row-border);
    }
    .wb-table thead th:first-child { border-radius: 10px 0 0 10px; text-align: center; }
    .wb-table thead th:last-child  { border-radius: 0 10px 10px 0; }
    .wb-th-hint {
        margin-inline-start: .25rem;
        padding: .05rem .4rem;
        border-radius: 6px;
        background: var(--wb-accent-soft);
        color: var(--wb-accent);
        font-size: .62rem;
        letter-spacing: .04em;
    }

    /* Each exercise = one <tbody> with a main row + 2 detail rows (media + notes) */
    .wb-table tbody.exercise-row { counter-increment: wb-exercise; }
    .wb-table tbody.exercise-row td {
        background: var(--wb-row-bg);
        padding: .55rem .45rem;
        vertical-align: top;
        border-top: 1px solid var(--wb-row-border);
        border-bottom: 1px solid var(--wb-row-border);
        transition: background-color .18s ease !important;
    }
    .wb-table tbody.exercise-row .wb-row-main td { border-bottom: 0; padding-top: .8rem; }
    .wb-table tbody.exercise-row .wb-row-detail td { border-top: 0; }
    .wb-table tbody.exercise-row .wb-row-media td { border-top: 0; border-bottom: 0; padding-top: .25rem; padding-bottom: .35rem; }
    .wb-table tbody.exercise-row .wb-row-notes td { border-top: 0; padding-top: .25rem; padding-bottom: .8rem; }
    .wb-table tbody.exercise-row td.wb-cell-idx {
        border-inline-start: 3px solid transparent;
        border-start-start-radius: 12px;
        border-end-start-radius: 12px;
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid var(--wb-row-border);
    }
    .wb-table tbody.exercise-row td.wb-cell-action {
        border-inline-end: 1px solid var(--wb-row-border);
        border-start-end-radius: 12px;
        border-end-end-radius: 12px;
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid var(--wb-row-border);
    }
    .wb-table tbody.exercise-row:hover td { background: var(--wb-row-hover); }
    .wb-table tbody.exercise-row:hover td.wb-cell-idx,
    .wb-table tbody.exercise-row:focus-within td.wb-cell-idx { border-inline-start-color: var(--wb-accent); }

    .wb-idx::before {
        content: counter(wb-exercise);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: var(--wb-accent-soft);
        color: var(--wb-accent);
        font-weight: 800;
        font-size: .82rem;
        font-variant-numeric: tabular-nums;
    }

    .wb-mini-label {
        display: block;
        margin-bottom: .3rem;
        color: var(--wb-text-muted);
        font-size: .66rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    /* Inputs inside the table (beats gym-polish global !important rules) */
    .wb-scope .wb-table .form-control {
        min-height: 40px !important;
        padding: .5rem .65rem !important;
        background-color: var(--wb-input-bg) !important;
        border: 1px solid var(--wb-input-border) !important;
        border-radius: 9px !important;
        color: var(--wb-text) !important;
        font-size: .9rem !important;
        font-weight: 500;
        box-shadow: none !important;
    }
    .wb-scope .wb-table .form-control:hover { border-color: var(--wb-input-border-hover) !important; }
    .wb-scope .wb-table .form-control:focus {
        border-color: var(--wb-accent) !important;
        box-shadow: 0 0 0 3px var(--wb-ring) !important;
        outline: none !important;
    }
    .wb-scope .wb-table .form-control::placeholder { color: var(--wb-text-muted) !important; opacity: .7; }
    .wb-scope .wb-table .wb-num {
        text-align: center;
        font-variant-numeric: tabular-nums;
        font-weight: 700;
    }
    .wb-scope .wb-table .wb-name { font-weight: 700; }
    .wb-scope .wb-table .wb-notes {
        resize: none;
        overflow: hidden;
        line-height: 1.45;
    }

    .wb-input-icon { position: relative; }
    .wb-input-icon > i {
        position: absolute;
        top: 50%;
        inset-inline-start: .7rem;
        transform: translateY(-50%);
        color: var(--wb-text-muted);
        pointer-events: none;
        font-size: .9rem;
    }
    .wb-scope .wb-table .wb-input-icon .form-control { padding-inline-start: 2.05rem !important; }

    /* Exercise Video Link & Preview Action */
    .wb-video-link-wrap {
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .wb-video-link-wrap .wb-input-icon {
        flex: 1;
        min-width: 0;
    }
    .wb-video-preview-btn {
        width: 40px;
        height: 40px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 9px;
        background: rgba(0, 158, 247, 0.12);
        color: #009ef7;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        flex-shrink: 0;
        font-size: 0.95rem;
        padding: 0;
        text-decoration: none;
    }
    .wb-video-preview-btn:hover:not(.is-disabled):not(:disabled) {
        background: #009ef7;
        border-color: #009ef7;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(0, 158, 247, 0.35);
    }
    .wb-video-preview-btn.is-disabled,
    .wb-video-preview-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        background: rgba(148, 163, 184, 0.08);
        border-color: rgba(255, 255, 255, 0.06);
        color: #94a3b8;
        transform: none;
        box-shadow: none;
    }

    /* Weight group inside the table */
    .wb-table .weight-inputs { gap: .35rem; }
    .wb-table .weight-inputs__list { grid-template-columns: repeat(auto-fill, minmax(66px, 1fr)); gap: .35rem; }
    .wb-scope .wb-table .weight-inputs__item .form-control {
        text-align: center;
        padding-inline: .45rem 1.35rem !important;
    }
    .wb-scope .wb-table .weight-inputs__item:only-child .form-control { padding-inline: .45rem !important; }
    .wb-table .weight-inputs__add {
        width: 40px;
        height: 40px;
        border-radius: 9px;
        background: var(--wb-accent-soft) !important;
        color: var(--wb-accent) !important;
        border: 1px dashed var(--wb-accent) !important;
    }
    .wb-table .weight-inputs__add i { color: inherit !important; }
    .wb-table .weight-inputs__add:not(:disabled):hover { background: var(--wb-accent) !important; color: #0F172A !important; }

    /* Row delete */
    .wb-delete {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 10px;
        background: var(--wb-danger-soft);
        color: var(--wb-danger);
        cursor: pointer;
        transition: background-color .15s ease, color .15s ease, transform .15s ease !important;
    }
    .wb-delete:hover { background: var(--wb-danger); color: #fff; transform: scale(1.06); }
    .wb-delete:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(244, 63, 94, .35); }

    .wb-exercises__footer {
        padding: .5rem 1.25rem 1.25rem;
    }
    .wb-add {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        width: 100%;
        padding: .8rem 1rem;
        border: 1.5px dashed var(--wb-row-border);
        border-radius: 12px;
        background: transparent;
        color: var(--wb-text-muted);
        font-weight: 700;
        font-size: .9rem;
        cursor: pointer;
        transition: border-color .15s ease, color .15s ease, background-color .15s ease !important;
    }
    .wb-add:hover, .wb-add:focus-visible {
        border-color: var(--wb-accent);
        color: var(--wb-accent);
        background: var(--wb-accent-soft);
        outline: none;
    }

    .wb-row-enter { animation: wbRowIn .25s ease-out; }
    .wb-row-leave { animation: wbRowOut .2s ease-in forwards; }
    @keyframes wbRowIn  { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: none; } }
    @keyframes wbRowOut { to   { opacity: 0; transform: translateY(-4px); } }

    /* ------------------------------------------------------------------
       Responsive: stack each exercise into a card below lg
       ------------------------------------------------------------------ */
    @media (max-width: 991.98px) {
        .wb-table, .wb-table tbody, .wb-table tr, .wb-table td { display: block; width: 100%; }
        .wb-table colgroup, .wb-table thead { display: none; }
        .wb-table { border-spacing: 0; }
        .wb-table tbody.exercise-row {
            position: relative;
            margin-bottom: .85rem;
            padding: .9rem;
            background: var(--wb-row-bg);
            border: 1px solid var(--wb-row-border);
            border-inline-start: 3px solid var(--wb-accent);
            border-radius: 12px;
        }
        .wb-exercises--warmup .wb-table tbody.exercise-row,
        .wb-exercises--post .wb-table tbody.exercise-row {
            border-color: rgba(167, 139, 250, 0.25);
            border-inline-start-color: #A78BFA;
        }
        .wb-table tbody.exercise-row td,
        .wb-table tbody.exercise-row:hover td {
            padding: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent;
        }
        .wb-row-main, .wb-row-detail {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .65rem;
        }
        .wb-row-detail { margin-top: .65rem; grid-template-columns: 1fr; }
        .wb-row-media { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .wb-row-notes { grid-template-columns: 1fr; margin-top: .45rem; }
        .wb-table td.wb-cell-name,
        .wb-table td.wb-cell-weights { grid-column: 1 / -1; }
        .wb-table td.wb-cell-name { padding-inline-end: 2.75rem !important; }
        .wb-table td.wb-cell-idx { display: none; }
        .wb-table td.wb-cell-action {
            position: absolute;
            top: .8rem;
            inset-inline-end: .8rem;
            width: auto;
        }
        .wb-table td[data-label]::before {
            content: attr(data-label);
            display: block;
            margin-bottom: .3rem;
            color: var(--wb-text-muted);
            font-size: .66rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
        }
        .wb-table .wb-mini-label { display: none; }
    }
    @media (max-width: 575.98px) {
        .wb-row-main { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .wb-row-media { grid-template-columns: 1fr; }
        .wb-exercises__header { flex-wrap: wrap; }
    }
    @media (prefers-reduced-motion: reduce) {
        .wb-row-enter, .wb-row-leave { animation: none; }
    }
</style>
