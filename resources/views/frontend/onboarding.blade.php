<!DOCTYPE html>
<html lang="my">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soma Yoga | Personalize Your Journey</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght=300;400;500;600;700&display=swap"
        rel="stylesheet">

    @include('layouts.onboard_style')

    <style>
        /* Smooth styling for disabled cards and custom input fields */
        .option-card.disabled-card {
            opacity: 0.5;
            pointer-events: none;
            background-color: #f5f5f5;
        }

        .custom-input-container {
            display: none;
            margin-top: 15px;
            grid-column: span 2;
            width: 100%;
        }

        .conInput {
            width: 100%;
            padding: 12px 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: border-color 0.2s;
        }

        .conInput:focus {
            border-color: var(--yoga-primary, #c49a72);
        }

        /* Symmetric 2-column grid layout */
        .grid-wrapper {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            width: 100%;
        }

        .option-card {
            width: 100%;
            box-sizing: border-box;
        }

        .toggle-others-btn,
        .toggle-none-btn {
            grid-column: span 2;
            width: 100%;
        }
    </style>
</head>

<body>

    <!-- STEP 1 -->
    <div class="step-panel active" id="step-1">
        <div class="split-container">
            <div class="split-sidebar">
                <div class="lotus-icon">
                    <svg width="45" height="45" fill="none" stroke="currentColor" stroke-width="1.2"
                        viewBox="0 0 24 24">
                        <path d="M12 22c4.5-5 7-9.5 7-13A7 7 0 0 0 5 9c0 3.5 2.5 8 7 13Z" />
                        <path d="M12 22c-2-4-4-7.5-4-13a4 4 0 1 1 8 0c0 5.5-2 9-4 13Z" />
                        <path d="M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                    </svg>
                </div>

                <h1 class="title-huge">
                    Welcome to SOMA, {{ auth()->user()->name ?? '' }}!
                </h1>

                <p style="font-size: 1.2rem; color: var(--yoga-text);">
                    Let’s find classes that inspire you.
                </p>
            </div>

            <div class="split-content">
                <h2 class="question-text">Where are you starting your practice today?</h2>

                <div class="error-msg" id="error-step-1">⚠️ Please make a selection.</div>
                <div class="subtitle-text">&nbsp;</div>

                <div class="cards-wrapper">
                    <div class="option-card">
                        <span class="indicator-box indicator-radio"></span>
                        <span class="option-text">I’m brand new to yoga</span>
                    </div>

                    <div class="option-card">
                        <span class="indicator-box indicator-radio"></span>
                        <span class="option-text">I’m coming back to it</span>
                    </div>

                    <div class="option-card">
                        <span class="indicator-box indicator-radio"></span>
                        <span class="option-text">I already practice regularly</span>
                    </div>
                </div>

                <div class="d-flex justify-content-end align-items-center gap-5">
                    <button type="button" class="mt-5" onclick="openTermsModal()" style="
                        background: none;
                        border: none;
                        font-size: 0.85rem;
                        color: var(--yoga-primary);
                        text-decoration: underline;
                        padding: 0;
                        cursor: pointer;
                    ">
                        View Terms & Privacy
                    </button>

                    <button class="btn-next" id="nextBtn" onclick="nextStep(2)" disabled>
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 2 -->
    <div class="step-panel" id="step-2">
        <div class="full-container">
            <button class="btn-back" onclick="prevStep(1)">‹</button>

            <h2 class="question-text">
                Aside from yoga, what else would you like to include in your practice?
            </h2>

            <p class="subtitle-text">Please select all that apply.</p>

            <div class="error-msg" id="error-step-2">⚠️ Please make a selection.</div>

            <div class="grid-wrapper">
                <div class="option-card"><span class="indicator-box indicator-checkbox"></span><span
                        class="option-text">Fitness</span></div>
                <div class="option-card"><span class="indicator-box indicator-checkbox"></span><span
                        class="option-text">Education</span></div>
                <div class="option-card"><span class="indicator-box indicator-checkbox"></span><span
                        class="option-text">Pilates</span></div>
                <div class="option-card"><span class="indicator-box indicator-checkbox"></span><span
                        class="option-text">Walking</span></div>
                <div class="option-card"><span class="indicator-box indicator-checkbox"></span><span
                        class="option-text">Meditation</span></div>
                <div class="option-card"><span class="indicator-box indicator-checkbox"></span><span
                        class="option-text">Stretching</span></div>
                <div class="option-card"><span class="indicator-box indicator-checkbox"></span><span
                        class="option-text">Barre</span></div>
                <div class="option-card"><span class="indicator-box indicator-checkbox"></span><span
                        class="option-text">Breathwork</span></div>
            </div>

            <button class="btn-next mt-5" onclick="nextStep(3)">Next</button>
        </div>
    </div>

    <!-- STEP 3 -->
    <div class="step-panel" id="step-3">
        <div class="full-container-3">
            <button class="btn-back" onclick="prevStep(2)">‹</button>

            <h2 class="question-text">What time of day do you prefer to practice?</h2>
            <p class="subtitle-text">Please select all that apply.</p>

            <div class="error-msg" id="error-step-3">⚠️ Please make a selection.</div>

            <div class="cards-wrapper">
                <div class="option-card"><span class="indicator-box indicator-checkbox"></span><span
                        class="option-text">Morning</span></div>
                <div class="option-card"><span class="indicator-box indicator-checkbox"></span><span
                        class="option-text">Afternoon</span></div>
                <div class="option-card"><span class="indicator-box indicator-checkbox"></span><span
                        class="option-text">Evening</span></div>
            </div>

            <button class="btn-next" onclick="nextStep(4)">Next</button>
        </div>
    </div>

    <!-- STEP 4 -->
    <div class="step-panel" id="step-4">
        <div class="full-container">
            <button class="btn-back" onclick="prevStep(3)">‹</button>

            <h2 class="question-text">Any special considerations for your practice?</h2>
            <p class="subtitle-text">Please select all that apply.</p>

            <div class="error-msg" id="error-step-4">⚠️ Please make a selection.</div>

            <div class="grid-wrapper">
                <div class="option-card consideration-option"><span
                        class="indicator-box indicator-checkbox"></span><span class="option-text">Aging</span></div>
                <div class="option-card consideration-option"><span
                        class="indicator-box indicator-checkbox"></span><span class="option-text">Mental health</span>
                </div>
                <div class="option-card consideration-option"><span
                        class="indicator-box indicator-checkbox"></span><span class="option-text">Injury or mobility
                        limitation</span></div>
                <div class="option-card consideration-option"><span
                        class="indicator-box indicator-checkbox"></span><span class="option-text">Illness</span></div>
                <div class="option-card consideration-option"><span
                        class="indicator-box indicator-checkbox"></span><span class="option-text">Menopause</span></div>
                <div class="option-card consideration-option"><span
                        class="indicator-box indicator-checkbox"></span><span class="option-text">Pregnancy or
                        postpartum</span></div>

                <div class="option-card toggle-none-btn" style="grid-column: span 2; max-width: 100%;">
                    <span class="indicator-box indicator-checkbox"></span>
                    <span class="option-text">None of the above</span>
                </div>

                <div class="custom-input-container" id="customConsiderContainer">
                    <input type="text" placeholder="Please specify your special consideration..." class="conInput"
                        id="customConsiderInput" />
                </div>
            </div>

            <button class="btn-next mt-5" onclick="nextStep(5)">Next</button>
        </div>
    </div>

    <!-- STEP 5 -->
    <div class="step-panel" id="step-5">
        <div class="full-container">
            <button class="btn-back" onclick="prevStep(4)">‹</button>

            <h2 class="question-text">Where do you know about Soma?</h2>
            <p class="subtitle-text">Please select One</p>

            <div class="error-msg" id="error-step-5">⚠️ Please make a selection.</div>

            <div class="grid-wrapper">
                <div class="option-card know-where-option"><span class="indicator-box indicator-radio"></span><span
                        class="option-text">Facebook</span></div>
                <div class="option-card know-where-option"><span class="indicator-box indicator-radio"></span><span
                        class="option-text">Tiktok</span></div>
                <div class="option-card know-where-option"><span class="indicator-box indicator-radio"></span><span
                        class="option-text">Instagram</span></div>
                <div class="option-card know-where-option"><span class="indicator-box indicator-radio"></span><span
                        class="option-text">Friend</span></div>

                <div class="option-card toggle-others-btn" style="grid-column: span 2; max-width: 100%;">
                    <span class="indicator-box indicator-radio"></span>
                    <span class="option-text">Others</span>
                </div>

                <div class="custom-input-container" id="customKnowWhereContainer">
                    <input type="text" placeholder="Please specify how you heard about us..." class="conInput"
                        id="customKnowWhereInput" />
                </div>
            </div>

            <button class="btn-next mt-5" onclick="startLoadingStep()">Next</button>
        </div>
    </div>

    <!-- STEP 6 (LOADER) -->
    <div class="step-panel" id="step-6">
        <div class="loader-container">
            <h2 style="font-weight: 400; color: #222;">
                We are personalizing your<br>Soma experience
            </h2>

            <div class="tree-icon">
                <svg width="60" height="60" fill="none" stroke="#222" stroke-width="1.2" viewBox="0 0 24 24">
                    <path d="M12 2L3 17h18L12 2Z" />
                    <path d="M12 7l-6 10h12L12 7Z" />
                </svg>
            </div>

            <p style="font-size: 0.9rem; color: #555; letter-spacing: 0.5px;">
                Soma is here to evolve with you day by day
            </p>
        </div>
    </div>

    <!-- STEP 7 (PACKAGES) -->
    <div class="step-panel" id="step-7" style="background-color: white;">
        <div class="full-container" style="padding-top: 3rem;">
            <div class="mt-5 text-center">
                <button type="button" class="btn-skip-full" onclick="skipPackageSelection()">
                    Skip for Now
                </button>
            </div>

            <div class="container mt-4">
                <div class="row justify-content-center">
                    @include('frontend.package_card', [
                        'packages' => $packages,
                        'buttonAction' => 'finishOnboarding'
                    ])
                </div>
            </div>
        </div>
    </div>

    <!-- TERMS MODAL -->
    <div id="termsModal" class="terms-backdrop">
        <div id="modalSheet" class="sheet">
            <div class="handle-bar">
                <div class="handle"></div>
            </div>

                   
            <div class="legal-content p-4">
                <div class="terms-container" style="background: #FAF8F5; padding: 25px; border-radius: 12px; border: 1px solid rgba(190, 150, 118, 0.2);">
                    <h5 style="color: var(--soma-dark); margin-bottom: 20px;">Agreement Terms & Conditions</h5>

                    <div style="font-size: 14px; line-height: 1.8; color: #555;">
                        <ul style="list-style: none; padding: 0;">
          
                                                 <li class="mb-3">
                                <i class="fas fa-calendar-check" style="color: #c49a72; margin-right: 10px;"></i>
                                <strong>Validity:</strong> Packages are valid for 30 days starting from the payment date. Validity begins from your first attended class.
                            </li>
             
               
                                                              <li class="mb-3">
                                <i class="fas fa-ban" style="color: #c49a72; margin-right: 10px;"></i>
                                <strong>Non-Refundable:</strong> Class packs are non-refundable, non-transferable between individuals or studios, and <strong>cannot be extended</strong> under any circumstances (including injury or illness).
                            </li>

                               
                                                           <li class="mb-3">
                                <i class="fas fa-clock" style="color: #c49a72; margin-right: 10px;"></i>
                                <strong>Cancellation Policy:</strong> Cancellations or rescheduling must be made at least <strong>24 hours</strong> prior to your class. Late cancellations or no-shows will result in the loss of that class credit with no refund provided.
                            </li>

                                                           <li class="mb-3">
                                <i class="fas fa-mobile-alt" style="color: #c49a72; margin-right: 10px;"></i>
                                <strong>Studio Etiquette:</strong> Please keep mobile devices in silent mode before entering the studio.
                            </li>

                                                           <li class="mb-3">
                                <i class="fas fa-door-open" style="color: #c49a72; margin-right: 10px;"></i>
                                <strong>Room Access:</strong> Please wait in the designated area until your scheduled session time. Kindly exit promptly upon conclusion to allow for space preparation.
                          
                             </li>
                        </ul>

                        <p style="font-size: 12px; color: #999; margin-top: 20px; border-top: 1px solid #e1dcd6; padding-top: 15px;">
                            <em>Note: Terms & conditions are subject to change without prior notice.</em>
                        </p>
                    </div>
                </div>
            </div>

            <div class="actions">
                <button type="button" class="btn-decline" onclick="declineTerms()">Decline</button>
                <button type="button" class="btn-accept" onclick="acceptTerms()">Accept</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        let termsAccepted = false;

        $(document).ready(function () {
            // General Option Card click handler
            $('.option-card').click(function () {
                let currentStepPanel = $(this).closest('.step-panel');
                currentStepPanel.find('.error-msg').hide();
                currentStepPanel.find('.option-card').removeClass('error-border');

                // Single Selection Radio logic
                if ($(this).find('.indicator-radio').length > 0) {
                    $(this).siblings().removeClass('active');
                }

                // Normal toggle behavior
                if (!$(this).hasClass('disabled-card')) {
                    $(this).toggleClass('active');
                }
            });

            // Target Step 4 logic for "None of the above"
            $('#step-4 .toggle-none-btn').click(function () {
                let isNoneSelected = $(this).hasClass('active');

                if (isNoneSelected) {
                    $('#step-4 .consideration-option').removeClass('active').addClass('disabled-card');
                    $('#customConsiderContainer').slideDown('fast');
                } else {
                    $('#step-4 .consideration-option').removeClass('disabled-card');
                    $('#customConsiderContainer').slideUp('fast');
                    $('#customConsiderInput').val('');
                }
            });

            // Target Step 5 logic for "Others"
            $('#step-5 .option-card').click(function () {
                let isOthers = $(this).hasClass('toggle-others-btn');
                if (isOthers && $(this).hasClass('active')) {
                    $('#customKnowWhereContainer').slideDown('fast');
                } else {
                    $('#customKnowWhereContainer').slideUp('fast');
                    $('#customKnowWhereInput').val('').css('border-color', '#ccc');
                }
            });
 
            // Clear red error border when user types into Others input
            $('#customKnowWhereInput').on('input', function() {
                if ($(this).val().trim() !== "") {
                    $(this).css('border-color', '#ccc');
                    $('#error-step-5').fadeOut('fast');
                }
            });

            openTermsModal();
        });

        if (window.location.hash === '#package-results') {
            $('.step-panel').removeClass('active');
            $('#step-6').addClass('active');
        }
 
        document.querySelectorAll('.soma-pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('href').includes('#package-results')) {
                    localStorage.setItem('onboarding_jump_step6', 'true');
                }
            });
         });

        $(document).ready(function() {
            if (localStorage.getItem('onboarding_jump_step6') === 'true') {
                localStorage.removeItem('onboarding_jump_step6');
                $('.step-panel').removeClass('active');
                $('#step-6').addClass('active');

                setTimeout(() => {
                    const target = document.getElementById('package-results');
                    if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 300);
            }
        });

        // Navigation Controller
        function nextStep(stepNumber) {
            if (!termsAccepted && stepNumber === 2) {
                openTermsModal();
                return false;
            }

            let currentStepNum = stepNumber - 1;
            let currentStepId = '#step-' + currentStepNum;
            let hasSelection = $(currentStepId).find('.option-card.active').length > 0;

            if (!hasSelection) {
                $('#error-step-' + currentStepNum).fadeIn('fast');
                $(currentStepId).find('.option-card').addClass('error-border');
                return false;
            }

            $('.step-panel').removeClass('active');
            $('#step-' + stepNumber).addClass('active');
        }

        function prevStep(stepNumber) {
            $('.step-panel').removeClass('active');
            $('#step-' + stepNumber).addClass('active');
        }

        function startLoadingStep() {
            let hasSelection = $('#step-5').find('.option-card.active').length > 0;

            if (!hasSelection) {
                $('#error-step-5').text('⚠️ Please make a selection.').fadeIn('fast');
                $('#step-5').find('.option-card').addClass('error-border');
                return false;
            }

            // Validation: Check if Others is selected and text input is empty
            if ($('#step-5 .toggle-others-btn').hasClass('active')) {
                let customWhere = $('#customKnowWhereInput').val().trim();
                if (customWhere === "") {
                    $('#customKnowWhereInput').css('border-color', '#dc3545').focus();
                    $('#error-step-5').text('⚠️ Please specify where you heard about us.').fadeIn('fast');
                    return false;
                }
            }

            $('.step-panel').removeClass('active');
            $('#step-6').addClass('active');

            setTimeout(function () {
                $('.step-panel').removeClass('active');
                $('#step-7').addClass('active');
            }, 2000);
        }

        function getOnboardingData() {
            let startingLevel = $('#step-1').find('.option-card.active .option-text').text().trim();

            let includedPractices = [];
            $('#step-2 .option-card.active .option-text').each(function () {
                includedPractices.push($(this).text().trim());
            });

            let preferredTimes = [];
            $('#step-3 .option-card.active .option-text').each(function () {
                preferredTimes.push($(this).text().trim());
            });

            let considerations = [];
            if ($('#step-4 .toggle-none-btn').hasClass('active')) {
                let customVal = $('#customConsiderInput').val().trim();
                considerations.push(customVal !== "" ? customVal : "None of the above");
            } else {
                $('#step-4 .consideration-option.active .option-text').each(function () {
                    considerations.push($(this).text().trim());
                });
            }

            let knowWhere = "";
            if ($('#step-5 .toggle-others-btn').hasClass('active')) {
                let customWhere = $('#customKnowWhereInput').val().trim();
                if (customWhere === "") {
                    $('#customKnowWhereInput').css('border-color', '#dc3545').focus();
                    $('#error-step-5').text('⚠️ Please specify where you heard about us.').fadeIn('fast');
                    return false;
                }
                knowWhere = customWhere; // Extract custom string value typed by user
            } else {
                knowWhere = $('#step-5 .know-where-option.active .option-text').text().trim();
            }

            return {
                startingLevel,
                includedPractices,
                preferredTimes,
                considerations,
                knowWhere
            };
        }

        function finishOnboarding(planType, buttonElement) {
            let data = getOnboardingData();
            if (!data) return false;

            let $clickedBtn = buttonElement ? $(buttonElement) : $('.btn-premium');

            $.ajax({
                url: "{{ route('onboarding.save') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    starting_level: data.startingLevel,
                    included_practices: data.includedPractices,
                    preferred_times: data.preferredTimes,
                    considerations: data.considerations,
                    know_where: data.knowWhere,
                    selected_plan: planType,
                    rules_accepted: termsAccepted
                },
                beforeSend: function () {
                    $('.btn-premium').prop('disabled', true);
                    $clickedBtn.text('Saving...');
                },
                success: function (response) {
                    if (response.status === 'success') {
                        window.location.href = '/rates';
                    }
                },
                error: function (xhr) {
                    alert('တစ်စုံတစ်ခုမှားယွင်းနေပါသည်။');
                    $('.btn-premium').prop('disabled', false).text('Choose plan');
                    console.log(xhr.responseText);
                }
            });
        }

        function skipPackageSelection() {
            let data = getOnboardingData();
            if (!data) return false;

            $.ajax({
                url: "{{ route('onboarding.save') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    starting_level: data.startingLevel,
                    included_practices: data.includedPractices,
                    preferred_times: data.preferredTimes,
                    considerations: data.considerations,
                    know_where: data.knowWhere,
                    selected_plan: null,
                    rules_accepted: termsAccepted
                },
                success: function (response) {
                    if (response.status === 'success') {
                        window.location.href = '/';
                    }
                },
                error: function (xhr) {
                    alert('တစ်စုံတစ်ခုမှားယွင်းနေပါသည်။');
                    console.log(xhr.responseText);
                }
            });
        }

        function openTermsModal() {
            const modal = document.getElementById('termsModal');
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }

        function closeTermsModal() {
            const modal = document.getElementById('termsModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 400);
        }

        function acceptTerms() {
            termsAccepted = true;
            enableNextButton();
            closeTermsModal();
        }

        function declineTerms() {
            termsAccepted = false;
            disableNextButton();
            closeTermsModal();
        }

        function enableNextButton() {
            const btn = document.getElementById('nextBtn');
            btn.disabled = false;
            btn.classList.remove('disabled');
        }

        function disableNextButton() {
            const btn = document.getElementById('nextBtn');
            btn.disabled = true;
            btn.classList.add('disabled');
        }
    </script>
</body>

</html>