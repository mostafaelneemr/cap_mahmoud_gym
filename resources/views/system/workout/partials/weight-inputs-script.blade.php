{{-- Shared styles + behaviour for the multi-weight input group (max 4). --}}
<style>
    .weight-inputs {
        display: flex;
        align-items: flex-start;
        gap: .4rem;
    }
    .weight-inputs__list {
        flex: 1 1 auto;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(72px, 1fr));
        gap: .4rem;
        min-width: 0;
    }
    .weight-inputs__item {
        position: relative;
        animation: weightInputIn .18s ease-out;
    }
    .weight-inputs__item .form-control {
        padding-left: .5rem;
        padding-right: 1.4rem;
    }
    .weight-inputs__remove {
        position: absolute;
        top: 50%;
        inset-inline-end: .3rem;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        border: 0;
        border-radius: 50%;
        background: rgba(241, 65, 108, .12);
        color: #f1416c;
        font-size: 14px;
        line-height: 18px;
        padding: 0;
        cursor: pointer;
        transition: background .15s ease, color .15s ease, transform .15s ease;
    }
    .weight-inputs__remove:hover {
        background: #f1416c;
        color: #fff;
        transform: translateY(-50%) scale(1.1);
    }
    /* The last remaining input cannot be removed */
    .weight-inputs__item:only-child .weight-inputs__remove {
        display: none;
    }
    .weight-inputs__item:only-child .form-control {
        padding-right: .5rem;
    }
    .weight-inputs__add {
        flex: 0 0 auto;
        height: calc(1.5em + 1.55rem + 2px);
        transition: opacity .15s ease, transform .15s ease;
    }
    .weight-inputs__add:not(:disabled):hover {
        transform: scale(1.06);
    }
    .weight-inputs__add:disabled {
        opacity: .35;
        cursor: not-allowed;
    }
    @keyframes weightInputIn {
        from { opacity: 0; transform: scale(.92); }
        to   { opacity: 1; transform: scale(1); }
    }
</style>

<script type="text/javascript">
    window.GymWeights = window.GymWeights || (function ($) {
        var MAX = {{ \App\Models\Exercise::MAX_WEIGHTS }};
        var REMOVE_LABEL = @json(__('Remove'));
        var ADD_LABEL = @json(__('Add Weight'));

        function itemHtml(inputName) {
            return '<div class="weight-inputs__item">' +
                '<input type="text" name="' + inputName + '" class="form-control form-control-solid text-center" placeholder="20kg" maxlength="20">' +
                '<button type="button" class="weight-inputs__remove" title="' + REMOVE_LABEL + '" aria-label="' + REMOVE_LABEL + '">&times;</button>' +
                '</div>';
        }

        /** Build an empty weight group. baseName has NO trailing [] */
        function build(baseName) {
            var inputName = baseName + '[]';
            return '<div class="weight-inputs" data-name="' + inputName + '" data-max="' + MAX + '">' +
                '<div class="weight-inputs__list">' + itemHtml(inputName) + '</div>' +
                '<button type="button" class="btn btn-icon btn-sm btn-light-success weight-inputs__add" title="' + ADD_LABEL + '">' +
                '<i class="fas fa-plus"></i></button>' +
                '</div>';
        }

        function refresh($group) {
            var max = parseInt($group.data('max'), 10) || MAX;
            var count = $group.find('.weight-inputs__item').length;
            $group.find('.weight-inputs__add').prop('disabled', count >= max);
        }

        $(document).on('click', '.weight-inputs__add', function () {
            var $group = $(this).closest('.weight-inputs');
            var max = parseInt($group.data('max'), 10) || MAX;
            var $list = $group.find('.weight-inputs__list');
            if ($list.children('.weight-inputs__item').length >= max) {
                refresh($group);
                return;
            }
            var $item = $(itemHtml($group.data('name')));
            $list.append($item);
            $item.find('input').trigger('focus');
            refresh($group);
        });

        $(document).on('click', '.weight-inputs__remove', function () {
            var $group = $(this).closest('.weight-inputs');
            var $items = $group.find('.weight-inputs__item');
            if ($items.length > 1) {
                $(this).closest('.weight-inputs__item').remove();
            } else {
                $items.find('input').val('');
            }
            refresh($group);
        });

        /** Reset a group back to a single empty input (used when clearing an exercise row). */
        function reset($scope) {
            $scope.find('.weight-inputs').each(function () {
                var $group = $(this);
                $group.find('.weight-inputs__item').slice(1).remove();
                $group.find('.weight-inputs__item input').val('');
                refresh($group);
            });
        }

        $(function () {
            $('.weight-inputs').each(function () { refresh($(this)); });
        });

        return { build: build, refresh: refresh, reset: reset, MAX: MAX };
    })(jQuery);
</script>
