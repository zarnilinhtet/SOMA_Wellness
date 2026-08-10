<style>
    :root {
        --yoga-beige: #F5EFE6;
        --yoga-green: #5c8d89;
        --yoga-dark: #1A1A1A;
        --yoga-text: #4A4A4A;
    }

    body,
    html {
        font-family: 'Poppins', sans-serif;
        height: 100vh;
        margin: 0;
        overflow-x: hidden;
    }

    .btn-skip-full {
        background-color: transparent;
        color: #888;
        border: 1px solid #e0e0e0;
        border-radius: 50rem;
        padding: 0.6rem 3rem;
        font-size: 16px;
        font-weight: 700;
        transition: all 0.3s ease;
    }


    .btn-skip-full:hover {
        background: #f8f8f8 !important;
        border-color: #d0d0d0 !important;
        color: #333 !important;
    }


    /* BACKDROP MODAL */
    .terms-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        transition: opacity 0.3s ease;
        opacity: 0;
        padding: 20px;
        /* Prevents the modal from touching screen edges on mobile */
    }

    .terms-backdrop.show {
        display: flex;
        opacity: 1;
    }

    /* BOTTOM SHEET -> CENTERED MODAL */
    .sheet {
        background: white;
        width: 100%;
        max-width: 600px;
        /* Updated to all-around rounded corners since it no longer sits on the bottom edge */
        border-radius: 20px 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        /* Changed from sliding up from the bottom (translateY) to scaling smoothly in place */
        transform: scale(0.9) translateY(20px);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
        display: flex;
        flex-direction: column;
        max-height: 80vh;
        opacity: 0;
    }

    /* Updated active transition rule to support scale and opacity */
    .terms-backdrop.show .sheet {
        transform: scale(1) translateY(0);
        opacity: 1;
    }

    /* Optional: Since it's centered, you might want to hide the drag handle bar */
    .handle-bar {
        display: none;
    }

    .handle {
        width: 50px;
        height: 5px;
        background: #ccc;
        border-radius: 10px;
    }

    /* MODAL HEADER */
    .modal-header-custom {
        padding: 0 20px 15px 20px;
        border-b: 1px solid #eee;
    }

    .modal-header-custom h2 {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
        color: #222;
    }

    /* LEGAL CONTENT CONTAINER */
    .legal-content {
        padding: 20px;
        overflow-y: auto;
        font-size: 0.875rem;
        color: #555;
        line-height: 1.6;
    }

    .legal-section {
        margin-bottom: 20px;
    }

    .legal-section h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #111;
        margin-bottom: 8px;
    }

    .legal-section ul {
        padding-left: 20px;
        margin-bottom: 10px;
    }

    /* MODAL ACTIONS */
    .actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 15px 20px 25px 20px;
        border-top: 1px solid #eee;
        background: white;
    }

    .actions button {
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-decline {
        background: #eee;
        color: #333;
    }

    .btn-decline:hover {
        background: #e0e0e0;
    }

    .btn-accept {
        background: black;
        color: white;
    }

    .btn-accept:hover {
        background: #222;
    }


    .btn-next {
        background-color: #1A1A1A;
        color: white;
        border: none;
        border-radius: 50rem;
        padding: 0.6rem 2.2rem;
        font-size: 16px;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    /* DISABLED STATE FOR ONBOARDING NEXT BUTTON */
    .btn-next.disabled {
        background-color: #ccc !important;
        color: #777 !important;
        cursor: not-allowed !important;
        opacity: 0.7;
    }

    .step-panel {
        display: none;
        height: 100vh;
        width: 100vw;
    }


    .step-panel.active {
        display: flex;
    }


    .split-container {
        display: flex;
        width: 100%;
        height: 100%;
    }

    .split-sidebar {
        background-color: var(--yoga-beige);
        width: 30%;
        padding: 4.5rem 3.5rem;
    }

    .split-content {
        width: 72%;
        padding: 4.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        position: relative;
    }


    .full-container {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        padding: 5rem 2rem;
        position: relative;
    }

    .full-container-3 {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        padding: 5rem 2rem;
        position: relative;
    }

    .lotus-icon {
        color: #A084DC;
        margin-bottom: 2rem;
    }

    .title-huge {
        font-size: 2.8rem;
        font-weight: 700;
        color: var(--yoga-dark);
        line-height: 1.2;
        margin-bottom: 2.5rem;
    }

    .question-text {
        font-size: 1.8rem;
        font-weight: 500;
        color: var(--yoga-dark);
        text-align: center;
        margin-bottom: 0.5rem;
    }

    .subtitle-text {
        font-size: 0.9rem;
        color: #7f8c8d;
        margin-bottom: 3rem;
        text-align: center;
    }

    /* Selection Cards */
    .cards-wrapper {
        width: 100%;
        max-width: 500px;
    }

    .grid-wrapper {
        width: 100%;
        max-width: 800px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.2rem;
    }

    .option-card {
        background: #ffffff;
        border: 1.5px solid #D3D3D3;
        border-radius: 16px;
        padding: 1.2rem 1.5rem;
        margin-bottom: 1.2rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s ease;
    }

    .grid-wrapper .option-card {
        margin-bottom: 0;
    }

    /* Radio & Checkbox Indicators */
    .indicator-box {
        width: 20px;
        height: 20px;
        border: 2px solid #A6A6A6;
        display: inline-block;
        flex-shrink: 0;
        position: relative;
    }

    .indicator-radio {
        border-radius: 50%;
    }

    .indicator-checkbox {
        border-radius: 6px;
    }


    .option-card.active {
        border-color: var(--yoga-green);
        background-color: #F4F9F8;
    }

    .option-card.active .indicator-box {
        border-color: var(--yoga-green);
        background-color: var(--yoga-green);
    }

    .option-card.active .indicator-box::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 8px;
        height: 8px;
        background-color: white;
        border-radius: 50%;
    }

    .split-content .btn-next {
        position: static;
        margin-top: 3rem;
        align-self: flex-end;
        margin-right: calc(0% - 400px + 15rem);
    }

    .full-container .btn-next {
        position: static;
        margin-top: 3rem;
        align-self: flex-end;
        margin-right: calc(30% - 400px + 15rem);
    }

    .full-container-3 .btn-next {
        position: static;
        margin-top: 3rem;
        align-self: flex-end;
        margin-right: calc(40% - 400px + 15rem);
    }

    .btn-next:hover {
        background-color: #333;
    }


    .btn-back {
        position: absolute;
        top: 4.5rem;
        left: 5rem;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--yoga-dark);
    }


    .error-msg {
        color: #BA3C3C;
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 1.5rem;
        text-align: center;
        display: none;
    }


    .option-card.error-border {
        border-color: #BA3C3C !important;
        background-color: #FFFBFB;
    }


    .loader-container {
        background-color: #FCDAD7;
        width: 100vw;
        height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .tree-icon {
        margin: 2rem 0;
        animation: pulse 1.5s infinite ease-in-out;
    }


    .pricing-header {
        text-align: center;
        margin-bottom: 1rem;
        width: 100%;
    }

    .pricing-wrapper {
        display: flex;
        gap: 3rem;
        width: 100%;
        align-items: flex-start;
    }

    .features-table {
        flex: 1.2;
    }

    .plans-column {
        flex: 0.8;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .plan-box {
        background: white;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        position: relative;
    }

    .plan-box.best-value {
        border-color: #C1F2D6;
        background-color: #FAFFFC;
    }

    .badge-value {
        background-color: #C1F2D6;
        color: #155724;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 20px;
        position: absolute;
        top: -12px;
        left: 1.5rem;
    }

    .btn-plan {
        background-color: #1A1A1A;
        color: white;
        border: none;
        width: 100%;
        padding: 0.75rem;
        border-radius: 50rem;
        font-weight: 500;
        margin-top: 1rem;
    }

    .btn-plan-outline {
        background-color: white;
        color: #1A1A1A;
        border: 1.5px solid #1A1A1A;
    }

    /* --- RESPONSIVE ADJUSTMENTS --- */

    @media (max-width: 991px) {

        /* Stack the split container vertically */
        .split-container {
            flex-direction: column;
            overflow-y: auto;
        }

        .split-sidebar {
            width: 100%;
            padding: 3rem 2rem 1rem 2rem;
            text-align: center;
        }

        .split-content {
            width: 100%;
            padding: 2rem;
        }

        /* Reset button positions for mobile */
        .btn-next,
        .split-content .btn-next,
        .full-container .btn-next,
        .full-container-3 .btn-next {
            margin-right: 0 !important;
            margin-top: 2rem !important;
            width: 100%;
            max-width: 300px;
            align-self: center !important;
        }

        /* Adjust grid for smaller screens */
        .grid-wrapper {
            grid-template-columns: 1fr;
            gap: 0.8rem;
        }

        .btn-back {
            top: 2rem;
            left: 2rem;
        }

        /* Adjust pricing container */
        .pricing-wrapper {
            flex-direction: column;
            gap: 1.5rem;
            padding: 0 1rem;
        }
    }

    /* Ensure the modal is mobile-friendly */
    @media (max-width: 576px) {
        .sheet {
            max-height: 90vh;
            border-radius: 20px;
        }

        .terms-container {
            padding: 15px !important;
        }
    }

    /* Fix for containers on small screens */
    .full-container,
    .full-container-3 {
        padding: 4rem 1.5rem !important;
    }

    .title-huge {
        font-size: 2rem;
    }

    .question-text {
        font-size: 1.4rem;
    }

    @keyframes pulse {
        0 {
            transform: scale(1);
            opacity: 0.8;
        }

        50% {
            transform: scale(1.08);
            opacity: 1;
        }

        100% {
            transform: scale(1);
            opacity: 0.8;
        }
    }
</style>