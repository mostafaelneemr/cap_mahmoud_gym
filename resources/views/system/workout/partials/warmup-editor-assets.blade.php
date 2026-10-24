{{--
    Warmup Rich Text Editor Assets & Styling (Quill.js 2 + Quill Table Better)
    Used across Workout Create/Edit and Trainee Show views.
--}}
@once
    <!-- Quill.js 2 Theme & Quill Table Better Styles -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill-table-better@1.2.3/dist/quill-table-better.css" rel="stylesheet">

    <style>
        /* ==========================================================================
           Warmup Quill WYSIWYG Editor Styling (Dark Slate & Light Modes)
           ========================================================================== */
        .warmup-quill-wrapper {
            position: relative;
            border-radius: 0.65rem;
            background: var(--wb-input-bg, rgba(15, 23, 42, 0.65));
            border: 1px solid var(--wb-input-border, #334155);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.2);
            overflow: visible;
        }

        .warmup-quill-wrapper:focus-within {
            border-color: var(--wb-accent, #facc15) !important;
            box-shadow: 0 0 0 2px rgba(250, 204, 21, 0.18), inset 0 1px 2px rgba(0, 0, 0, 0.2) !important;
        }

        /* Toolbar */
        .warmup-quill-wrapper .ql-toolbar.ql-snow {
            background: rgba(30, 41, 59, 0.85);
            border: none !important;
            border-bottom: 1px solid var(--wb-row-border, #334155) !important;
            border-top-left-radius: 0.65rem;
            border-top-right-radius: 0.65rem;
            padding: 6px 10px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px;
        }

        .warmup-quill-wrapper .ql-toolbar.ql-snow .ql-formats {
            margin-right: 8px;
            margin-bottom: 0;
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }

        /* Toolbar SVG Icons (Stroke & Fill) */
        .warmup-quill-wrapper .ql-snow .ql-stroke {
            stroke: #94a3b8;
            stroke-width: 1.8;
            transition: stroke 0.15s ease;
        }

        .warmup-quill-wrapper .ql-snow .ql-fill {
            fill: #94a3b8;
            transition: fill 0.15s ease;
        }

        .warmup-quill-wrapper .ql-snow .ql-picker {
            color: #94a3b8;
        }

        .warmup-quill-wrapper .ql-snow button {
            width: 28px;
            height: 28px;
            padding: 4px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.15s ease;
            position: relative;
        }

        .warmup-quill-wrapper .ql-snow button:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .warmup-quill-wrapper .ql-snow button:hover .ql-stroke,
        .warmup-quill-wrapper .ql-snow button.ql-active .ql-stroke {
            stroke: var(--wb-accent, #facc15) !important;
        }

        .warmup-quill-wrapper .ql-snow button:hover .ql-fill,
        .warmup-quill-wrapper .ql-snow button.ql-active .ql-fill {
            fill: var(--wb-accent, #facc15) !important;
        }

        /* Editor Area */
        .warmup-quill-wrapper .ql-container.ql-snow {
            border: none !important;
            font-family: inherit;
            font-size: 0.925rem;
            border-bottom-left-radius: 0.65rem;
            border-bottom-right-radius: 0.65rem;
        }

        .warmup-quill-wrapper .ql-editor {
            min-height: 110px;
            max-height: 320px;
            overflow-y: auto;
            color: var(--wb-text, #f8fafc);
            padding: 12px 14px;
            line-height: 1.6;
        }

        .warmup-quill-wrapper .ql-editor.ql-blank::before {
            color: #64748b;
            font-style: italic;
            left: 14px;
            right: 14px;
        }

        /* Links inside Quill Editor & Display Content */
        .warmup-quill-wrapper .ql-editor a,
        .warmup-rich-content a {
            color: #06b6d4 !important; /* Vibrant Cyan */
            text-decoration: underline !important;
            font-weight: 600 !important;
            transition: color 0.15s ease, opacity 0.15s ease;
            word-break: break-all;
        }

        .warmup-quill-wrapper .ql-editor a:hover,
        .warmup-rich-content a:hover {
            color: #22d3ee !important;
            opacity: 0.9;
        }

        /* Quill Link Tooltip */
        .warmup-quill-wrapper .ql-snow .ql-tooltip {
            background: #0f172a !important;
            border: 1px solid #475569 !important;
            color: #f8fafc !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5) !important;
            padding: 6px 12px !important;
            z-index: 1050;
        }

        .warmup-quill-wrapper .ql-snow .ql-tooltip input[type="text"] {
            background: #1e293b !important;
            border: 1px solid #475569 !important;
            color: #f8fafc !important;
            border-radius: 6px !important;
            padding: 5px 8px !important;
            font-size: 0.85rem !important;
            margin-right: 6px;
        }

        .warmup-quill-wrapper .ql-snow .ql-tooltip a.ql-action::after {
            color: var(--wb-accent, #facc15) !important;
            font-weight: 600;
        }

        .warmup-quill-wrapper .ql-snow .ql-tooltip a.ql-remove::before {
            color: #f87171 !important;
        }

        /* ==========================================================================
           Warmup Tables (Quill Editor & Display Screens)
           ========================================================================== */
        .warmup-quill-wrapper .ql-editor table,
        .warmup-rich-content table {
            width: 100% !important;
            max-width: 100%;
            border-collapse: collapse !important;
            margin: 12px 0 !important;
            border-radius: 8px;
            background: rgba(15, 23, 42, 0.45);
            border: 1px solid var(--wb-row-border, #334155) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .warmup-quill-wrapper .ql-editor table th,
        .warmup-rich-content table th {
            background: rgba(30, 41, 59, 0.95) !important;
            color: var(--wb-accent, #facc15) !important;
            font-weight: 700 !important;
            font-size: 0.84rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 10px 12px !important;
            border: 1px solid var(--wb-row-border, #334155) !important;
            text-align: inherit;
        }

        .warmup-quill-wrapper .ql-editor table td,
        .warmup-rich-content table td {
            padding: 8px 12px !important;
            border: 1px solid var(--wb-row-border, #334155) !important;
            color: var(--wb-text, #f8fafc) !important;
            font-size: 0.88rem;
            vertical-align: middle;
            min-width: 60px;
        }

        .warmup-quill-wrapper .ql-editor table tr:nth-child(even),
        .warmup-rich-content table tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.025);
        }

        .warmup-quill-wrapper .ql-editor table tr:hover,
        .warmup-rich-content table tr:hover {
            background: rgba(250, 204, 21, 0.04);
        }

        /* Quill Table Better Picker & Context Menus (Dark Mode) */
        .ql-table-select-container {
            background: #0f172a !important;
            border: 1px solid #475569 !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.6) !important;
            padding: 6px !important;
            z-index: 1060 !important;
        }

        .ql-table-select-container .ql-table-select-label {
            color: #94a3b8 !important;
            font-weight: 600;
        }

        .ql-table-select-container span {
            border: 1px solid #475569 !important;
            background: #1e293b !important;
            border-radius: 2px;
        }

        .ql-table-select-container span.ql-table-select-selected {
            background: #facc15 !important;
            border-color: #eab308 !important;
        }

        .ql-table-dropdown-list,
        .ql-table-dropdown-properties-list,
        .ql-table-properties-form {
            background: #0f172a !important;
            border: 1px solid #475569 !important;
            color: #f8fafc !important;
            border-radius: 8px !important;
            box-shadow: 0 12px 28px -5px rgba(0, 0, 0, 0.65) !important;
            z-index: 1060 !important;
        }

        .ql-table-dropdown-list li,
        .ql-table-dropdown-properties-list li {
            color: #f8fafc !important;
            padding: 4px 12px !important;
            font-size: 0.85rem;
            transition: background 0.15s ease;
        }

        .ql-table-dropdown-list li:hover,
        .ql-table-dropdown-properties-list li:hover {
            background-color: #1e293b !important;
            color: #facc15 !important;
        }

        .ql-table-properties-form .properties-form-header {
            color: #f8fafc !important;
            border-bottom-color: #334155 !important;
        }

        /* ==========================================================================
           Light Mode Overrides
           ========================================================================== */
        [data-bs-theme="light"] .warmup-quill-wrapper {
            background: #ffffff;
            border-color: #cbd5e1;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        [data-bs-theme="light"] .warmup-quill-wrapper:focus-within {
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2) !important;
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-toolbar.ql-snow {
            background: #f8fafc;
            border-bottom-color: #e2e8f0 !important;
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-snow .ql-stroke {
            stroke: #475569;
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-snow .ql-fill {
            fill: #475569;
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-snow button:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-snow button:hover .ql-stroke,
        [data-bs-theme="light"] .warmup-quill-wrapper .ql-snow button.ql-active .ql-stroke {
            stroke: #d97706 !important;
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-snow button:hover .ql-fill,
        [data-bs-theme="light"] .warmup-quill-wrapper .ql-snow button.ql-active .ql-fill {
            fill: #d97706 !important;
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-editor {
            color: #0f172a;
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-editor.ql-blank::before {
            color: #94a3b8;
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-editor a,
        [data-bs-theme="light"] .warmup-rich-content a {
            color: #0284c7 !important;
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-editor a:hover,
        [data-bs-theme="light"] .warmup-rich-content a:hover {
            color: #0369a1 !important;
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-snow .ql-tooltip {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12) !important;
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-snow .ql-tooltip input[type="text"] {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        /* Light Mode Table Overrides */
        [data-bs-theme="light"] .warmup-quill-wrapper .ql-editor table,
        [data-bs-theme="light"] .warmup-rich-content table {
            background: #ffffff;
            border-color: #cbd5e1 !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-editor table th,
        [data-bs-theme="light"] .warmup-rich-content table th {
            background: #f1f5f9 !important;
            color: #b45309 !important;
            border-color: #cbd5e1 !important;
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-editor table td,
        [data-bs-theme="light"] .warmup-rich-content table td {
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-editor table tr:nth-child(even),
        [data-bs-theme="light"] .warmup-rich-content table tr:nth-child(even) {
            background: #f8fafc;
        }

        [data-bs-theme="light"] .warmup-quill-wrapper .ql-editor table tr:hover,
        [data-bs-theme="light"] .warmup-rich-content table tr:hover {
            background: rgba(217, 119, 6, 0.04);
        }

        [data-bs-theme="light"] .ql-table-select-container,
        [data-bs-theme="light"] .ql-table-dropdown-list,
        [data-bs-theme="light"] .ql-table-dropdown-properties-list,
        [data-bs-theme="light"] .ql-table-properties-form {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15) !important;
        }

        [data-bs-theme="light"] .ql-table-dropdown-list li,
        [data-bs-theme="light"] .ql-table-dropdown-properties-list li {
            color: #0f172a !important;
        }

        [data-bs-theme="light"] .ql-table-dropdown-list li:hover,
        [data-bs-theme="light"] .ql-table-dropdown-properties-list li:hover {
            background-color: #f1f5f9 !important;
            color: #d97706 !important;
        }

        /* ==========================================================================
           Display Container (View Mode) Styling
           ========================================================================== */
        .warmup-rich-content {
            line-height: 1.65;
            word-break: break-word;
            overflow-x: auto;
        }

        /* Responsive Table Container inside Display Area */
        .warmup-rich-content .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0.75rem 0;
            border-radius: 8px;
        }

        .warmup-rich-content table td p,
        .warmup-rich-content table th p,
        .warmup-quill-wrapper .ql-editor table td p,
        .warmup-quill-wrapper .ql-editor table th p {
            margin-bottom: 0 !important;
        }

        .warmup-rich-content p {
            margin-bottom: 0.5rem;
        }

        .warmup-rich-content p:last-child {
            margin-bottom: 0;
        }

        .warmup-rich-content ul,
        .warmup-rich-content ol {
            padding-inline-start: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .warmup-rich-content li {
            margin-bottom: 0.25rem;
        }

        /* ==========================================================================
           Theme-Aware Section Headings & Descriptions
           Light Mode: Black text (#000000)
           Dark Mode: White text (#ffffff)
           ========================================================================== */
        .wb-rich-title,
        .wb-rich-section h4 {
            color: #ffffff !important;
            transition: color 0.15s ease;
        }

        .wb-rich-desc,
        .wb-rich-section .wb-rich-desc,
        .wb-rich-section p.text-muted {
            color: #ffffff !important;
            transition: color 0.15s ease;
        }

        /* Light Mode: black text for headings & descriptions */
        [data-bs-theme="light"] .wb-rich-title,
        [data-bs-theme="light"] .wb-rich-section h4,
        html[data-bs-theme="light"] .wb-rich-title,
        html[data-bs-theme="light"] .wb-rich-section h4,
        body[data-bs-theme="light"] .wb-rich-title,
        body[data-bs-theme="light"] .wb-rich-section h4 {
            color: #000000 !important;
        }

        [data-bs-theme="light"] .wb-rich-desc,
        [data-bs-theme="light"] .wb-rich-section .wb-rich-desc,
        [data-bs-theme="light"] .wb-rich-section p.text-muted,
        html[data-bs-theme="light"] .wb-rich-desc,
        html[data-bs-theme="light"] .wb-rich-section .wb-rich-desc,
        html[data-bs-theme="light"] .wb-rich-section p.text-muted,
        body[data-bs-theme="light"] .wb-rich-desc,
        body[data-bs-theme="light"] .wb-rich-section .wb-rich-desc,
        body[data-bs-theme="light"] .wb-rich-section p.text-muted {
            color: #000000 !important;
        }

        /* Dark Mode: white text for headings & descriptions */
        [data-bs-theme="dark"] .wb-rich-title,
        [data-bs-theme="dark"] .wb-rich-section h4,
        html[data-bs-theme="dark"] .wb-rich-title,
        html[data-bs-theme="dark"] .wb-rich-section h4 {
            color: #ffffff !important;
        }

        [data-bs-theme="dark"] .wb-rich-desc,
        [data-bs-theme="dark"] .wb-rich-section .wb-rich-desc,
        [data-bs-theme="dark"] .wb-rich-section p.text-muted,
        html[data-bs-theme="dark"] .wb-rich-desc,
        html[data-bs-theme="dark"] .wb-rich-section .wb-rich-desc,
        html[data-bs-theme="dark"] .wb-rich-section p.text-muted {
            color: #ffffff !important;
        }
    </style>

    <!-- Quill.js 2 & Quill Table Better CDN -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-table-better@1.2.3/dist/quill-table-better.js"></script>

    <script type="text/javascript">
        window.WarmupQuill = window.WarmupQuill || (function ($) {
            var instances = {};
            var tableBetterRegistered = false;

            // Register QuillTableBetter module once
            if (window.Quill && window.QuillTableBetter && !tableBetterRegistered) {
                try {
                    Quill.register({
                        'modules/table-better': QuillTableBetter
                    }, true);
                    tableBetterRegistered = true;
                } catch (e) {
                    console.warn('QuillTableBetter registration note:', e);
                }
            }

            /**
             * Check if Quill editor content is effectively empty
             */
            function isEmpty(quill) {
                if (!quill) return true;
                // If contains table elements, it's not empty
                if (quill.root && quill.root.querySelector('table')) {
                    return false;
                }
                var text = quill.getText().trim();
                return text.length === 0;
            }

            /**
             * Extract safe HTML content from Quill editor
             */
            function getHtml(quill) {
                if (!quill || isEmpty(quill)) {
                    return '';
                }
                var html = quill.root.innerHTML;
                // If it's just an empty paragraph, return empty string
                if (html === '<p><br></p>' || html === '<p></p>') {
                    return '';
                }
                return html;
            }

            /**
             * Ensure all links inside a container open safely in a new tab
             */
            function makeLinksSafe(container) {
                var $c = $(container || document);
                $c.find('.warmup-rich-content a, .ql-editor a').each(function () {
                    $(this).attr({
                        'target': '_blank',
                        'rel': 'noopener noreferrer'
                    });
                });
            }

            var tableSvgIcon = '<svg viewBox="0 0 18 18"><rect class="ql-stroke" height="12" width="14" x="2" y="3" rx="1.5"></rect><line class="ql-stroke" x1="2" x2="16" y1="8" y2="8"></line><line class="ql-stroke" x1="2" x2="16" y1="12" y2="12"></line><line class="ql-stroke" x1="7" x2="7" y1="3" y2="15"></line><line class="ql-stroke" x1="11" x2="11" y1="3" y2="15"></line></svg>';

            /**
             * Safely populate HTML content into Quill (handles tables via clipboard Delta conversion)
             */
            function setEditorContent(quill, html) {
                if (!quill || typeof html !== 'string' || !html.trim()) return;
                try {
                    if (html.indexOf('<table') !== -1 && quill.clipboard) {
                        var delta = quill.clipboard.convert({ html: html });
                        quill.setContents(delta, 'silent');
                    } else {
                        quill.root.innerHTML = html;
                    }
                } catch (e) {
                    quill.root.innerHTML = html;
                }
            }

            /**
             * Initialize a Quill editor instance on an element or selector
             */
            function init(containerSelector, hiddenInputSelector, initialContent) {
                var $container = $(containerSelector);
                if (!$container.length) return null;

                var containerElem = $container[0];

                // If already initialized on this DOM node, update content if provided and return existing
                if (containerElem.__quillInstance) {
                    if (typeof initialContent === 'string' && initialContent.length > 0) {
                        setEditorContent(containerElem.__quillInstance, initialContent);
                    }
                    return containerElem.__quillInstance;
                }

                var placeholder = $container.data('placeholder') || "{{ __('Warm up instructions, stretch routine, embed video links...') }}";

                // Setup Quill toolbar options with table and heading support
                var toolbarOptions = [
                    [{ 'header': [2, 3, 4, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'bullet' }, { 'list': 'ordered' }],
                    ['link'],
                    ['table-better'],
                    ['clean']
                ];

                var modulesConfig = {
                    table: false,
                    toolbar: toolbarOptions
                };

                if (tableBetterRegistered) {
                    modulesConfig['table-better'] = {
                        language: 'en_US',
                        menus: ['column', 'row', 'merge', 'table', 'cell', 'wrap', 'delete'],
                        toolbarTable: true
                    };
                    if (window.QuillTableBetter && QuillTableBetter.keyboardBindings) {
                        modulesConfig.keyboard = {
                            bindings: QuillTableBetter.keyboardBindings
                        };
                    }
                } else {
                    // Fallback to native table module if table-better not present
                    modulesConfig.table = true;
                }

                // Capture initial content before Quill clears or reconstructs DOM
                var initialHtml = (typeof initialContent === 'string' && initialContent.length > 0)
                    ? initialContent
                    : ($container.html().trim().length > 0 && $container.find('.ql-editor').length === 0 ? $container.html() : '');

                $container.empty();

                var quill = new Quill(containerElem, {
                    theme: 'snow',
                    modules: modulesConfig,
                    placeholder: placeholder
                });

                containerElem.__quillInstance = quill;
                var instanceKey = containerElem.id || containerSelector;
                instances[instanceKey] = {
                    quill: quill,
                    container: $container,
                    hiddenInputSelector: hiddenInputSelector
                };

                // Ensure button.ql-table-better displays a clean grid table icon
                setTimeout(function () {
                    var $toolbar = $container.closest('.warmup-quill-wrapper').find('.ql-toolbar');
                    var $tableBtn = $toolbar.find('button.ql-table-better');
                    if ($tableBtn.length && !$tableBtn.find('svg').length) {
                        $tableBtn.html(tableSvgIcon + '<span class="visually-hidden">Table</span>');
                        $tableBtn.attr('title', "{{ __('Insert Table') }}");
                    }
                }, 50);

                // Populate initial content if provided
                if (initialHtml) {
                    setEditorContent(quill, initialHtml);
                }

                // If hidden input selector provided, keep it in sync
                if (hiddenInputSelector) {
                    var $input = $(hiddenInputSelector);
                    if ($input.length) {
                        // Sync initial
                        $input.val(getHtml(quill));

                        // Sync on text changes
                        quill.on('text-change', function () {
                            $input.val(getHtml(quill));
                        });
                    }
                }

                // Auto-hook target="_blank" on links in editor on click
                quill.root.addEventListener('click', function (e) {
                    if (e.target && e.target.tagName === 'A') {
                        e.target.setAttribute('target', '_blank');
                        e.target.setAttribute('rel', 'noopener noreferrer');
                    }
                });

                return quill;
            }

            /**
             * Sync a specific editor's HTML to its hidden input
             */
            function sync(containerSelector) {
                var key = (typeof containerSelector === 'string')
                    ? containerSelector.replace(/^#/, '')
                    : $(containerSelector).attr('id');

                var item = instances[key] || instances['#' + key];
                if (item && item.quill && item.hiddenInputSelector) {
                    $(item.hiddenInputSelector).val(getHtml(item.quill));
                } else if (item && item.quill) {
                    var $input = item.container.closest('.warmup-quill-wrapper').find('.warmup-hidden-input');
                    if ($input.length) {
                        $input.val(getHtml(item.quill));
                    }
                }
            }

            /**
             * Sync all active Quill editors to their hidden inputs
             */
            function syncAll() {
                $.each(instances, function (key, item) {
                    if (item.quill) {
                        var html = getHtml(item.quill);
                        if (item.hiddenInputSelector && $(item.hiddenInputSelector).length) {
                            $(item.hiddenInputSelector).val(html);
                        } else {
                            var $input = item.container.closest('.warmup-quill-wrapper').find('.warmup-hidden-input');
                            if ($input.length) {
                                $input.val(html);
                            }
                        }
                    }
                });
            }

            /**
             * Sync all editors inside a given form
             */
            function syncForm(formSelector) {
                var $form = $(formSelector);
                $form.find('.warmup-quill-editor').each(function () {
                    var elem = this;
                    var q = elem.__quillInstance;
                    if (q) {
                        var html = getHtml(q);
                        var $wrapper = $(elem).closest('.warmup-quill-wrapper');
                        var $input = $wrapper.find('.warmup-hidden-input');
                        if (!$input.length) {
                            $input = $form.find(`input[name="warmup"]`);
                        }
                        if ($input.length) {
                            $input.val(html);
                        }
                    }
                });
            }

            /**
             * Reset an editor to empty
             */
            function reset(containerSelector) {
                var key = (typeof containerSelector === 'string')
                    ? containerSelector.replace(/^#/, '')
                    : $(containerSelector).attr('id');

                var item = instances[key] || instances['#' + key];
                if (item && item.quill) {
                    item.quill.setText('');
                    if (item.hiddenInputSelector) {
                        $(item.hiddenInputSelector).val('');
                    }
                }
            }

            // Auto-run safe links on document ready
            $(document).ready(function () {
                makeLinksSafe(document);
            });

            return {
                init: init,
                sync: sync,
                syncAll: syncAll,
                syncForm: syncForm,
                reset: reset,
                getHtml: getHtml,
                makeLinksSafe: makeLinksSafe
            };
        })(jQuery);
    </script>
@endonce
