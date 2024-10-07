"use strict";
var wizzerd;
var KTCreateAccount = (function () {
    var e,
        t,
        i,
        o,
        a,
        r,
        s = [];
    return {
        init: function () {
            (e = document.querySelector("#kt_modal_create_account")) &&
                new bootstrap.Modal(e),
                (t = document.querySelector("#kt_create_account_stepper")) &&
                ((i = t.querySelector("#kt_create_account_form")),
                    (o = t.querySelector('[data-kt-stepper-action="submit"]')),
                    (a = t.querySelector('[data-kt-stepper-action="next"]')),
                    (r = new KTStepper(t)).on(
                        "kt.stepper.changed",
                        function (e) {
                            2 === r.getCurrentStepIndex()
                                ? (o.classList.remove("d-none"),
                                    o.classList.add("d-inline-block"),
                                    a.classList.add("d-none"))
                                : 3 === r.getCurrentStepIndex()
                                    ? (o.classList.add("d-none"),
                                        a.classList.add("d-none"))
                                    : (o.classList.remove("d-inline-block"),
                                        o.classList.remove("d-none"),
                                        a.classList.remove("d-none"));
                        }
                    ),
                    r.on("kt.stepper.next", function (e) {
                        console.log("stepper.next");
                        var t = s[e.getCurrentStepIndex() - 1];
                        t
                            ? t.validate().then(function (t) {
                                console.log("validated!"),
                                    "Valid" == t
                                        ? (stepCtrl ? e.goNext() : '', KTUtil.scrollTop())
                                        : Swal.fire({
                                            text: swMessage ?? "Sorry, looks like there are some errors detected, please try again.",
                                            icon: "error",
                                            buttonsStyling: !1,
                                            confirmButtonText:
                                                swConfirm ?? "Ok, got it!",
                                            customClass: {
                                                confirmButton:
                                                    "btn btn-light",
                                            },
                                        }).then(function () {
                                            KTUtil.scrollTop();
                                        });
                            })
                            : (stepCtrl ? e.goNext() : '', KTUtil.scrollTop());
                    }),
                    r.on("kt.stepper.previous", function (e) {
                        console.log("stepper.previous"),
                            e.goPrevious(),
                            KTUtil.scrollTop();
                    }),
                    s.push(
                        FormValidation.formValidation(i, {
                            fields: {
                                name: {
                                    validators: {
                                        notEmpty: {
                                            message: nameTranslate ?? "This field is required",
                                        },
                                    },
                                },
                            },
                            plugins: {
                                trigger: new FormValidation.plugins.Trigger(),
                                bootstrap:
                                    new FormValidation.plugins.Bootstrap5({
                                        rowSelector: ".fv-row",
                                        eleInvalidClass: "",
                                        eleValidClass: "",
                                    }),
                            },
                        })
                    ));
            wizzerd = r;
        },
    };
})();
KTUtil.onDOMContentLoaded(function () {
    KTCreateAccount.init();
});
