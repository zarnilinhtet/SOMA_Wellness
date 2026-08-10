@extends('layouts.link')

@section('content')
    <div class="terms-page-wrapper py-5" style="background: #fdfcfb; min-height: 100vh;">
        <div class="terms-container">

            <ul class="nav justification-tabs mb-4 justify-content-center" id="termsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-3 py-2 d-flex align-items-center" id="processing-tab" type="button"
                        role="tab" aria-controls="processing-content" aria-selected="true">
                        <i class="fas fa-file-invoice-dollar me-2"></i> Payment Policy
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-3 py-2 d-flex align-items-center" id="agreement-tab" type="button" role="tab"
                        aria-controls="agreement-content" aria-selected="false">
                        <i class="fas fa-handshake me-2"></i> Agreement Terms
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="termsTabsContent">

                <div class="tab-pane fade show active" id="processing-content" role="tabpanel"
                    aria-labelledby="processing-tab">
                    <p class="fw-bold text-dark mb-4 fs-6">Please carefully read our standard payment policy before
                        completing your purchase:</p>
                    <ul class="terms-list">
                        <li>
                            <div class="icon-box"><i class="fas fa-bolt"></i></div>
                            <span>All payment requests are processed manually within 10 to 30 minutes under regular
                                processing hours.</span>
                        </li>
                        <li>
                            <div class="icon-box"><i class="fas fa-receipt"></i></div>
                            <span>You must provide a clear and authentic receipt voucher image/screenshot displaying the
                                corresponding global Transaction Reference Identifier Code.</span>
                        </li>
                        <li>
                            <div class="icon-box warning"><i class="fas fa-exclamation-triangle"></i></div>
                            <span>Falsified proof or multiple entries mapping a single voucher instance code will trigger
                                immediate automated system account terminal validation locks.</span>
                        </li>
                        <li>
                            <div class="icon-box"><i class="fas fa-hand-holding-usd"></i></div>
                            <span>Refunds are not permitted once process verification pipeline statuses settle into
                                completed execution branches.</span>
                        </li>
                    </ul>
                </div>

                <div class="tab-pane fade" id="agreement-content" role="tabpanel" aria-labelledby="agreement-tab">
                    <p class="fw-bold text-dark mb-4 fs-6">Studio Rules & Purchase Regulations:</p>
                    <ul class="terms-list">
                        <li>
                            <div class="icon-box"><i class="fas fa-calendar-check"></i></div>
                            <span><strong>Validity:</strong> Packages are valid for 30 days starting from the payment date.
                                Validity begins from your first attended class.</span>
                        </li>
                        <li>
                            <div class="icon-box danger"><i class="fas fa-ban"></i></div>
                            <span><strong>Non-Refundable:</strong> Class packs are non-refundable, non-transferable between
                                individuals or studios, and <strong>cannot be extended</strong> under any circumstances
                                (including injury or illness).</span>
                        </li>
                        <li>
                            <div class="icon-box"><i class="fas fa-clock"></i></div>
                            <span><strong>Cancellation Policy:</strong> Cancellations or rescheduling must be made at least
                                <strong>24 hours</strong> prior to your class. Late cancellations or no-shows will result in
                                the loss of that class credit with no refund provided.</span>
                        </li>
                        <li>
                            <div class="icon-box"><i class="fas fa-mobile-alt"></i></div>
                            <span><strong>Studio Etiquette:</strong> Please keep mobile devices in silent mode before
                                entering the studio.</span>
                        </li>
                        <li>
                            <div class="icon-box"><i class="fas fa-door-open"></i></div>
                            <span><strong>Room Access:</strong> Please wait in the designated area until your scheduled
                                session time. Kindly exit promptly upon conclusion to allow for space preparation.</span>
                        </li>
                    </ul>
                </div>
            </div>

            <p class="terms-footer-note">
                <i class="fas fa-info-circle me-1"></i> Note: Terms & conditions are subject to change without prior notice.
            </p>
        </div>
    </div>

    <style>
        .terms-container {
            background: #FAF8F5;
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(190, 150, 118, 0.2);
            max-width: 780px;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(190, 150, 118, 0.05);
        }

        /* Target the nav container */
        .justification-tabs {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            border-bottom: 2px solid rgba(190, 150, 118, 0.15) !important;
            /* Elegant background track line */
            gap: 20px;
        }

        .justification-tabs .nav-link {
            background: transparent !important;
            color: #888888 !important;
            border: none !important;
            font-weight: 500;
            font-size: 15px;
            position: relative;
            padding-bottom: 12px !important;
            margin-bottom: -2px !important;
            /* Perfectly overlaps the container line */
            border-bottom: 2px solid transparent !important;
            transition: all 0.2s ease;
        }

        .justification-tabs .nav-link:hover {
            color: #BE9676 !important;
        }

        /* Active State: Bold Text + Colored Underline */
        .justification-tabs .nav-link.active {
            color: #BE9676 !important;
            font-weight: 700 !important;
            /* Strong Font Weight */
            border-bottom: 2px solid #BE9676 !important;
            /* Branded Active Underline */
        }

        .terms-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .terms-list li {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
            font-size: 14.5px;
            line-height: 1.7;
            color: #4A4A4A;
        }

        .terms-list li:last-child {
            margin-bottom: 0;
        }

        .icon-box {
            background: rgba(190, 150, 118, 0.12);
            color: #BE9676;
            min-width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            margin-top: 2px;
            font-size: 13px;
        }

        .icon-box.warning {
            background: rgba(219, 148, 34, 0.12);
            color: #db9422;
        }

        .icon-box.danger {
            background: rgba(214, 40, 40, 0.1);
            color: #d62828;
        }

        .terms-footer-note {
            font-size: 12px;
            color: #999;
            margin-top: 35px;
            border-top: 1px solid rgba(190, 150, 118, 0.15);
            padding-top: 20px;
            margin-bottom: 0;
            text-align: center;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tabButtons = document.querySelectorAll('.justification-tabs button');
            const tabPanes = document.querySelectorAll('#termsTabsContent .tab-pane');

            tabButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();

                    tabButtons.forEach(btn => {
                        btn.classList.remove('active');
                        btn.setAttribute('aria-selected', 'false');
                    });

                    tabPanes.forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    this.classList.add('active');
                    this.setAttribute('aria-selected', 'true');

                    const targetId = this.getAttribute('aria-controls');
                    const targetPane = document.getElementById(targetId);
                    if (targetPane) {
                        targetPane.classList.add('active');
                        setTimeout(() => {
                            targetPane.classList.add('show');
                        }, 20);
                    }
                });
            });
        });
    </script>
@endsection