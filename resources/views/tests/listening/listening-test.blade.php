<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IELTS CDI Listening Practice</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            line-height: 1.4;
            font-size: 16px;
            padding-bottom: 90px; /* nav-row height (80px) + some extra spacing */
        }

        .header {
            background-color: #ffffff;
            padding: 12px 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            height: 60px;
        }

        .ielts-logo {
            display: none;
        }

        .test-info {
            display: flex;
            align-items: center;
            gap: 30px;
            font-size: 14px;
            color: #333;
        }

        .audio-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
        }

        .audio-icon {
            width: 16px;
            height: 16px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon></svg>') no-repeat center;
        }

        .header-icons {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .telegram-link {
            color: #0088cc;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .telegram-link:hover {
            text-decoration: underline;
        }

        .telegram-link::before {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            background-color: currentColor;
            -webkit-mask-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9.78 18.65l.28-4.23l7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3L3.64 12c-.88-.25-.89-1.37.2-1.64l16.56-6.4c.75-.29 1.4.22 1.2.94l-2.67 12.61c-.22.95-1.13 1.18-1.78.73l-4.5-3.32l-2.23 2.15c-.47.44-1.29.21-1.48-.37z"/></svg>');
            mask-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9.78 18.65l.28-4.23l7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3L3.64 12c-.88-.25-.89-1.37.2-1.64l16.56-6.4c.75-.29 1.4.22 1.2.94l-2.67 12.61c-.22.95-1.13 1.18-1.78.73l-4.5-3.32l-2.23 2.15c-.47.44-1.29.21-1.48-.37z"/></svg>');
            background-size: contain;
            background-repeat: no-repeat;
        }

        #volume-slider {
            -webkit-appearance: none;
            width: 80px;
            height: 4px;
            background: #ddd;
            outline: none;
            border-radius: 2px;
            cursor: pointer;
        }

        #volume-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #333;
            cursor: pointer;
        }

        #volume-slider::-moz-range-thumb {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #333;
            cursor: pointer;
            border: none;
        }

        .icon {
            width: 20px;
            height: 20px;
            cursor: pointer;
            opacity: 0.7;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon:hover {
            opacity: 1;
        }

        #play-pause-btn {
            background: none;
            border: none;
            padding: 0;
        }

        .main-container {
            margin-top: 60px;
            display: flex;
            background: #ffffff;
            padding-bottom: 100px; /* Space for bottom nav */
        }

        .main-container.results-mode {
            display: flex;
            flex-direction: row;
        }

        /* Left Panel */
        .left-panel {
            width: 100%;
            padding: 20px;
            transition: width 0.4s ease;
        }

        .main-container.results-mode .left-panel {
            width: 50%;
            overflow-y: auto;
            height: calc(100vh - 195px);
        }

        /* Right Panel */
        .right-panel {
            display: none;
            width: 50%;
            padding: 20px;
            position: relative;
            border-left: 1px solid #e0e0e0;
            overflow-y: auto;
            height: calc(100vh - 195px);
        }

        #transcription-container h2 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        #transcription-text {
            white-space: pre-wrap;
            font-family: inherit;
            font-size: 15px;
            line-height: 1.7;
        }

        .transcription-instruction {
            display: block;
            margin: 1em 0;
            font-style: italic;
            color: #444;
        }

        .main-container.results-mode .right-panel {
            display: block;
            width: 50%;
            border-left: 1px solid #e0e0e0;
            border-top: none;
            height: calc(100vh - 195px);
            overflow-y: auto;
        }

        .help-button {
            background: #4a90e2;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 20px;
        }

        .correct-answer-text {
            color: #28a745;
            font-weight: bold;
            margin-left: 10px;
        }

        .help-button:hover {
            background: #357abd;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .people-section {
            margin-bottom: 30px;
        }

        .people-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ccc;
        }

        .people-table th {
            text-align: left;
            padding: 10px;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        .people-table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }

        .person-name {
            font-size: 16px;
        }

        .question-number {
            background: white;
            border: 1px solid #4a90e2;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 14px;
            color: #4a90e2;
            min-width: 30px;
            text-align: center;
        }

        .assigned-responsibility {
            background: #f0f0f0;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 14px;
            border: 1px solid #ddd;
        }

        .responsibilities-section {
            margin-bottom: 30px;
        }

        .responsibility-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 20px;
        }

        .responsibility-item {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: move;
            font-size: 16px;
            text-align: center;
            transition: all 0.2s;
        }

        .responsibility-item:hover {
            background: #e9ecef;
            transform: translateY(-1px);
        }

        .responsibility-item.dragging {
            opacity: 0.5;
            transform: rotate(2deg);
        }

        .questions-section {
            margin-bottom: 20px;
        }

        .question-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .instruction {
            margin-bottom: 20px;
            font-size: 16px;
            color: #666;
        }

        .centered-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        /* Map Styles */
        .map-container {
            position: relative;
            margin: 20px auto;
            width: 100%;
            max-width: 600px;
            display: block;
        }
        .map-container img {
            width: 100%;
            display: block;
        }
        .map-drop-zone {
            position: absolute;
            width: 13%;
            height: 15%;
            border: 2px dashed #999;
            background-color: rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #555;
            border-radius: 4px;
            padding: 2px;
        }
        .map-drop-zone:hover {
            background-color: rgba(230, 244, 255, 0.7);
            border-color: #4a90e2;
        }
        .map-drop-zone .drag-item {
            font-size: 16px;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px;
        }
        .map-options-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .map-options-list .drag-item {
            width: calc(25% - 10px);
            text-align: center;
        }

        /* Navigation Arrows */
        .nav-arrows {
            position: fixed;
            bottom: 100px;
            right: 20px;
            display: flex;
            gap: 5px;
            z-index: 101;
        }

        .nav-arrow {
            width: 50px;
            height: 50px;
            background: #333;
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: bold;
        }

        .nav-arrow:hover {
            background: #555;
        }

        .nav-arrow:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Bottom Navigation - Exact Match */
        .nav-row {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;

            padding: 0;
            display: flex;
            align-items: center;
            height: 80px;
            z-index: 100;

            /* Allow horizontal scrolling on smaller screens */
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        .footer__questionWrapper___1tZ46 {
            display: flex;
            align-items: center;
            margin-right: 20px;
            flex-shrink: 0;
        }


        .footer__questionNo___3WNct {
            background: none;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: 600;
            color: #333;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.2s;
        }

        .footer__questionNo___3WNct:hover {
            background-color: #f8f9fa;
        }

        .section-prefix {
            font-size: 16px;
        }

        .sectionNr {
            font-size: 16px;
            font-weight: bold;
        }

        .attemptedCount {
            font-size: 14px;
            color: #666;
            margin-left: 5px;
            font-weight: 400; /* Normal weight */
        }

        @media (max-width: 1024px) {
            .attemptedCount {
                display: none;
            }
        }

        .footer__questionWrapper___1tZ46.selected .attemptedCount {
            display: none;
        }

        .footer__subquestionWrapper___9GgoP {
            display: none;
            gap: 2px;
            margin-left: 10px;
        }

        .footer__questionWrapper___1tZ46.selected .footer__subquestionWrapper___9GgoP {
            display: flex;
        }

        .subQuestion {
            width: 32px;
            height: 32px;
            border: 1px solid #ccc;
            background: white;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            border-radius: 2px;
        }

        .subQuestion.answered {
            background-color: #e9ecef;
            border-color: #ddd;
        }
        .subQuestion.correct {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }
        .subQuestion.incorrect {
            background-color: #dc3545;
            color: white;
            border-color: #dc3545;
        }

        .subQuestion:hover {
            background-color: #f0f0f0;
            border-color: #999;
        }

        .subQuestion.active {
            background-color: #4a90e2;
            color: white;
            border-color: #4a90e2;
        }

        .subQuestion.completed {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .footer__deliverButton___3FM07 {
            margin-left: auto;
            margin-right: 20px;
            background-color: #f0f0f0;
            color: #333;
            border: 1px solid #ccc;
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s, border-color 0.2s;
            min-width: 170px;
            justify-content: center;
        }

        .footer__deliverButton___3FM07:hover {
            background-color: #e0e0e0;
            border-color: #bbb;
        }

        .footer__deliverButton___3FM07:disabled {
            background: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
            border-color: #ddd;
        }

        .fa-check::before {
            content: "✓";
        }

        .hidden {
            display: none;
        }

        /* Context Menu */
        .context-menu {
            position: absolute;
            background: white;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            display: none;
            min-width: 140px;
        }

        .context-menu-item {
            padding: 12px 16px;
            cursor: pointer;
            font-size: 16px;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .context-menu-item:hover {
            background-color: #f8f9fa;
        }

        .context-menu-item:last-child {
            border-bottom: none;
        }

        .highlight {
            background-color: #ffff00;
        }

        .comment-highlight {
            background-color: #90EE90;
            position: relative;
            cursor: help;
        }

        .comment-tooltip {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            white-space: nowrap;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .comment-highlight:hover .comment-tooltip {
            opacity: 1;
        }

        .question {
            margin-bottom: 40px;
        }
        .question p {
            margin-bottom: 10px;
        }
        .question ul, .question .people-table {
            margin-top: 0;
        }
        .question-prompt {
            margin-bottom: 20px;
        }
        .question ul {
            list-style: none;
            padding-left: 0;
        }
        .question ul li {
            margin-bottom: 5px;
        }
        .answer-input {
            border: 1px solid #888;
            border-radius: 4px;
            background-color: #fff;
            padding: 4px 8px;
            font-size: 16px;
            text-align: center;
            margin-right: 4px;
            margin-left: 4px;
            width: 140px;
            max-width: 160px;
            min-width: 120px;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            height: 24px;
            min-height: 24px;
            line-height: 1.2;
            vertical-align: middle;
        }
        .answer-input::placeholder {
            color: #999;
            font-weight: bold;
            text-align: center;
        }
        .answer-input.correct {
            border-color: #28a745;
            background-color: #e9f7ef;
        }
        .answer-input.incorrect {
            border-color: #dc3545;
            background-color: #f8d7da;
            color: #721c24;
        }
        .answer-input {
    z-index: 10;
    position: relative;
    pointer-events: auto;
}
        .drag-drop-container {
            display: flex;
            gap: 30px;
            margin-top: 20px;
            align-items: flex-start;
        }
        .recommendations-box {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            min-height: 200px;
        }
        .drag-item {
            background: white;
            border: 1px solid #ddd;
            padding: 6px 8px;
            border-radius: 4px;
            cursor: move;
            font-size: 16px;
            transition: all 0.2s;
            user-select: none;
            word-break: break-word;
            overflow-wrap: break-word;
            text-align: center;
            line-height: 1.2;
            max-width: 100%;
            box-sizing: border-box;
        }
        .drag-item:hover {
            background: #e9ecef;
        }
        .drag-item.dragging {
            opacity: 0.5;
            transform: rotate(2deg);
        }
        .drop-zone {
            border: 1px dashed #ccc;
            border-radius: 4px;
            min-height: 40px;
            height: auto;
            max-width: 120px;
            min-width: 100px;
            transition: background-color 0.2s, border-color 0.2s;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            word-break: break-word;
            overflow-wrap: break-word;
            text-align: center;
            line-height: 1.2;
        }
        .drop-zone.drag-over {
            background-color: #e6f4ff;
            border-color: #4a90e2;
        }
        .drop-zone.correct {
            background-color: #e9f7ef;
            border-color: #28a745;
            border-style: solid;
        }
        .drop-zone.incorrect {
            background-color: #f8d7da;
            border-color: #dc3545;
            border-style: solid;
        }
        .drop-zone .drag-item {
            cursor: default;
        }
        .drag-item.selected {
            border-color: #4a90e2;
            box-shadow: 0 0 0 1px #4a90e2;
        }
        .questions-container {
            width: 50%;
        }
        audio {
            width: 100%;
            margin-bottom: 20px;
        }
        .part-header {
            background-color: #f1f2ec;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
        }
        .part-header p {
            margin: 0;
        }

        .question li.correct {
            color: #155724;
            font-weight: bold;
        }
        .question li.incorrect {
            color: #721c24;
        }
        .question li.correct::before {
            content: '✔ ';
            color: #28a745;
        }
        .question li.incorrect::before {
            content: '✖ ';
            color: #dc3545;
        }

        .example-box {
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .timer-container {
            display: none; /* Timer hidden */
            color: #333;
            font-size: 16px;
            font-weight: 500;
        }
        .timer-container .timer-tooltip {
            display: none;
        }

        .audio-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(40, 40, 40, 0.9);
            z-index: 2000;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }
        .audio-modal-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            max-width: 500px;
            padding: 20px;
        }
        .audio-modal-content p {
            font-size: 16px;
            line-height: 1.5;
            color: #eee;
        }
        .audio-modal-icon {
            margin-bottom: 20px;
        }
        .modal-play-btn {
            background-color: #000;
            color: white;
            border: 1px solid white;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }
        .modal-play-btn:hover {
            background-color: #333;
        }
        #goto-widget {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        #goto-input {
            width: 80px;
            padding: 4px 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        #goto-btn {
            padding: 4px 10px;
            border: 1px solid #ccc;
            background-color: #f0f0f0;
            border-radius: 4px;
            cursor: pointer;
        }

        @keyframes flash {
            0% { background-color: #e6f4ff; }
            100% { background-color: transparent; }
        }

        .question.flash {
            animation: flash 1s ease-out;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            z-index: 2000;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 8px;
            width: 100%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .modal-header h2 {
            font-size: 24px;
            color: #333;
        }
        .modal-close-btn {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #888;
            line-height: 1;
        }
        .modal-close-btn:hover {
            color: #333;
        }
        #score-summary {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        #result-details table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        #result-details th, #result-details td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            vertical-align: middle;
        }
        #result-details th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        #result-details td:nth-child(1) {
            font-weight: bold;
            text-align: center;
        }
        .result-correct {
            color: #28a745;
            font-weight: bold;
        }
        .result-incorrect {
            color: #dc3545;
            font-weight: bold;
        }

        .context-menu-icon {
            width: 16px;
            height: 16px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 8px;
        }

        .matching-container {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            margin-top: 20px;
            align-items: flex-start;
        }
        .matching-table {
            flex: 4;
            border-collapse: collapse;
        }
        .matching-table th, .matching-table td {
            border: 1px solid #ddd;
            padding: 6px 12px;
            text-align: left;
            vertical-align: middle;
        }
        .matching-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .matching-table .drop-zone {
            min-height: 35px;
            height: 35px;
            width: 100%;
            max-width: none;
            min-width: 300px;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: nowrap;
            background-color: #fff;
            border: 1px dashed #ccc;
            border-radius: 4px;
            word-break: break-word;
            overflow-wrap: break-word;
            text-align: left;
            line-height: 1.2;
        }
        .matching-table .drop-zone .drag-item {
            width: auto;
            max-width: none;
            text-align: left;
            padding: 6px 8px;
            font-size: 16px;
            line-height: 1.2;
            word-break: break-word;
            overflow-wrap: break-word;
            box-sizing: border-box;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin: 2px;
        }
        .matching-options-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex: 1;
            background-color: transparent;
            padding: 0;
            border: none;
            min-height: 250px;
            min-width: 280px; /* ensure enough width for long option text to stay on one line */
        }
        .matching-options-list .drag-item {
            width: 100%;
        }
        .matching-question-item .drop-zone .placeholder {
            color: #999;
            font-weight: bold;
        }

        .option strong,
        .question ul li label strong {
            display: none;
        }

        /* Mobile selection toolbar */
        .mobile-selection-menu {
            position: absolute;
            background: #333;
            color: #fff;
            padding: 6px 10px;
            border-radius: 6px;
            display: flex;
            gap: 8px;
            z-index: 3000;
            box-shadow: 0 2px 6px rgba(0,0,0,0.25);
        }
        .mobile-selection-menu button {
            background: transparent;
            border: 1px solid #fff;
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 14px;
        }
        .mobile-selection-menu.hidden {
            display: none;
        }

        /* === MCQ table styling for Questions 11-15 === */
        .mcq-table {
            width: 100%;
            border-collapse: collapse;
        }
        .mcq-table th,
        .mcq-table td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            vertical-align: middle;
            text-align: center;
        }
        .mcq-table th {
            background-color: #1e6de6; /* vibrant blue header like screenshot */
            color: #ffffff;
            font-weight: 600;
            text-align: center;
        }
        .mcq-table th:first-child {
            text-align: left;
        }
        .mcq-table tbody td:first-child {
            font-weight: 500;
            text-align: left;
        }
        .mcq-table tr:nth-child(even) td {
            background-color: #f8faff; /* subtle alternating row colour */
        }
        .mcq-table input[type="radio"] {
            transform: scale(1.1);
            cursor: pointer;
        }
        /* list of options (A, B, C) shown above the table */
        .mcq-options {
            list-style: none;
            padding-left: 0;
            margin: 10px 0;
        }
        .mcq-options li {
            margin-bottom: 4px;
            font-size: 16px;
        }

        /* Compact single-choice list */
        .single-choice-container { margin: 10px 0; display: flex; flex-direction: column; gap: 18px; }
        .single-choice label { display: block; margin-left: 20px; margin-top: 4px; font-size: 16px; }

        .aligned-form .question-row {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .aligned-form .question-label {
            width: 230px;
            padding-right: 10px;
        }

        /* Responsive adjustments for tablets */
        @media (max-width: 1024px) {
            .main-container {
                flex-direction: column;
            }
            .right-panel, .main-container.results-mode .right-panel {
                display: none;
            }
            .left-panel, .main-container.results-mode .left-panel {
                width: 100%;
                padding: 20px 10px;
                height: auto; /* Reset height for mobile */
            }
            .questions-container {
                width: 100%;
            }
        }

        .single-choice label.correct {
            color: #155724;
            font-weight: bold;
        }
        .single-choice label.correct::before {
            content: '✔ ';
            color: #28a745;
        }
        .single-choice label.incorrect {
            color: #721c24;
            text-decoration: line-through;
        }
        .single-choice label.incorrect::before {
            content: '✖ ';
            color: #dc3545;
        }
        .mcq-table tbody tr.correct {
            background-color: #d4edda !important;
            color: #155724;
            font-weight: bold;
        }
        .mcq-table tbody tr.incorrect {
            background-color: #f8d7da !important;
            text-decoration: line-through;
        }
        .mcq-table tbody tr.incorrect td {
            color: #721c24;
        }

        /* New Audio Player Styles */
        .audio-player-container {
            position: fixed;
            top: 65px;
            left: 50%;
            transform: translateX(-50%);
            width: 500px;
            max-width: 90%;
            height: 40px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            padding: 0 15px;
            z-index: 99;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .main-container {
            margin-top: 115px;
        }
        .player-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .player-btn svg {
            width: 20px;
            height: 20px;
            fill: #333;
        }
        .progress-container {
            flex-grow: 1;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        #progress-bar {
            flex-grow: 1;
            -webkit-appearance: none;
            appearance: none;
            height: 4px;
            background: #ddd;
            outline: none;
            border-radius: 3px;
            cursor: pointer;
        }
        #progress-bar::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #4a90e2;
            cursor: pointer;
        }
        #progress-bar::-moz-range-thumb {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #4a90e2;
            cursor: pointer;
            border: none;
        }
        #current-time, #total-duration {
            font-size: 12px;
            color: #555;
            min-width: 35px;
            text-align: center;
        }
        .controls-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .volume-container {
            display: flex;
            align-items: center;
            position: relative;
        }
        #new-volume-slider {
            -webkit-appearance: none;
            appearance: none;
            width: 60px;
            height: 3px;
            background: #ccc;
            outline: none;
            border-radius: 2px;
            cursor: pointer;
            margin-left: 8px;
            display: block;
        }

        #new-volume-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #333;
        }
        #new-volume-slider::-moz-range-thumb {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #333;
            border: none;
        }
        .speed-container {
            position: relative;
        }
        #speed-btn {
            font-size: 12px;
            font-weight: 600;
            color: #333;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 4px 8px;
        }
        #speed-options {
            position: absolute;
            top: calc(100% + 5px); /* Position below the button with a small gap */
            right: 0;
            background: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            z-index: 100;
        }
        #speed-options div {
            padding: 8px 15px;
            cursor: pointer;
        }
        #speed-options div:hover {
            background-color: #f0f0f0;
        }

        /* Flowchart styles */
        .flowchart-container {
            border: 1px solid #ccc;
            padding: 0 20px;
        }
        .flowchart-step {
            text-align: center;
            margin: 0;
            padding: 20px 0;
            border-top: 1px solid #ddd;
        }
        .flowchart-step:first-child {
            border-top: none;
            padding-top: 20px;
            margin-top: 0;
        }
        .flowchart-arrow {
            text-align: center;
            margin: 0;
            padding: 15px 0;
            font-size: 20px;
            color: #555;
            border-top: 1px solid #ddd;
        }
        .flowchart-split {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            gap: 20px;
            border-top: 1px solid #ddd;
        }
        .flowchart-split-col {
            width: 48%;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .flowchart-split .flowchart-step {
            border-top: none;
            padding-top: 0;
            margin-top: 0;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .flowchart-split .flowchart-arrow {
            border-top: none;
            padding: 15px 0;
        }


        /* Responsive adjustments for tablets */
        @media (max-width: 1024px) {
            .main-container {
                flex-direction: column;
            }
            .right-panel, .main-container.results-mode .right-panel {
                display: none;
            }
            .left-panel, .main-container.results-mode .left-panel {
                width: 100%;
                padding: 20px 10px;
                height: auto; /* Reset height for mobile */
            }
            .questions-container {
                width: 100%;
            }

            /* Adjust input boxes for tablets */
            .answer-input {
                width: 90px;
                max-width: 110px;
                font-size: 15px;
            }

            .drop-zone {
                max-width: 200px;
                min-width: 150px;
            }

            .matching-table .drop-zone {
                max-width: 200px;
                min-width: 150px;
            }
        }

        /* Responsive adjustments for mobile phones */
        @media (max-width: 768px) {
            .answer-input {
                width: 80px;
                max-width: 100px;
                font-size: 14px;
                padding: 2px 4px;
            }

            .drop-zone {
                max-width: 180px;
                min-width: 120px;
                font-size: 14px;
            }

            .matching-table .drop-zone {
                max-width: 180px;
                min-width: 120px;
            }

            .drag-item {
                font-size: 16px;
                padding: 4px 6px;
            }

            .matching-table .drop-zone .drag-item {
                font-size: 16px;
            }

            /* Ensure drag-drop containers are more mobile-friendly */
            .drag-drop-container {
                flex-direction: column;
                gap: 15px;
            }

            .matching-container {
                flex-direction: column;
                gap: 20px;
            }
        }

        /* Extra small screens */
        @media (max-width: 480px) {
            .answer-input {
                width: 70px;
                max-width: 90px;
                font-size: 13px;
            }

            .drop-zone {
                max-width: 90px;
                min-width: 70px;
            }

            .matching-table .drop-zone {
                max-width: 90px;
                min-width: 70px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="timer-container">
            <span class="timer-display">60:00</span>

        </div>
    </div>

    <div class="audio-player-container">
        <button id="play-pause-btn" class="player-btn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
        </button>
        <div class="progress-container">
            <span id="current-time">0:00</span>
            <input type="range" id="progress-bar" value="0" step="1" style="width: 100%;">
            <span id="total-duration">0:00</span>
        </div>
        <div class="controls-container">
            <div class="volume-container">
                <button id="volume-btn" class="player-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path></svg>
                </button>
                <input type="range" id="new-volume-slider" min="0" max="1" step="0.01" value="1">
            </div>
            <div class="speed-container">
                <button id="speed-btn" class="player-btn">1x</button>
                <div id="speed-options" class="hidden">
                    <div data-speed="0.5">0.5x</div>
                    <div data-speed="0.75">0.75x</div>
                    <div data-speed="1">1x</div>
                    <div data-speed="1.25">1.25x</div>
                    <div data-speed="1.5">1.5x</div>
                    <div data-speed="2">2x</div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-container">
        <!-- Left Panel -->
        <div class="left-panel">

            <div id="part-1" class="question-part">
                <div class="part-header">
                    <p><strong>Part 1</strong></p>
                    <p>Listen and answer questions 1–10.</p>
                </div>
                <div class="questions-container">
                    <div class="question">
                        <div class="question-prompt">
                            <p><strong>Questions 1-10</strong></p>
                            <p>Complete the form below.</p>
                            <p>Write <strong>NO MORE THAN TWO WORDS AND/OR A NUMBER</strong> for each answer.</p>
                        </div>
                        <div style="border: 1px solid #000000; padding: 15px;"> <p class="centered-title">Home Insurance Quotation Form</p>
                            <div style="font-family: Arial, sans-serif; line-height: 1.6;">
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <span style="width: 140px; font-weight: bold;">Name:</span>
                                    <span style="font-weight: bold;">Janet Evans</span>
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <span style="width: 140px; font-weight: bold;">Address:</span>
                                    <input type="text" id="q1" class="answer-input" placeholder="1" style="width: 120px; margin-right: 5px;"> Court
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <span style="width: 140px; font-weight: bold;">Email:</span>
                                    <input type="text" id="q2" class="answer-input" placeholder="2" style="width: 200px;">
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <span style="width: 140px; font-weight: bold;">Telephone number:</span>
                                    <span>(020) 4251-9443</span>
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <span style="width: 140px; font-weight: bold;">Best time to contact:</span>
                                    <input type="text" id="q3" class="answer-input" placeholder="3" style="width: 80px; margin-right: 5px;"> pm
                                </div>

                                <div style="margin: 15px 0 10px 0; font-weight: bold; font-size: 16px;">Property Information</div>

                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <span style="width: 140px; font-weight: bold;">Property size:</span>
                                    <input type="text" id="q4" class="answer-input" placeholder="4" style="width: 80px; margin-right: 5px;"> m²
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <span style="width: 140px; font-weight: bold;">Material(s):</span>
                                    <input type="text" id="q5" class="answer-input" placeholder="5" style="width: 120px;">
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <span style="width: 140px; font-weight: bold;">Security measures:</span>
                                    <input type="text" id="q6" class="answer-input" placeholder="6" style="width: 150px;">
                                </div>

                                <div style="margin: 15px 0 10px 0; font-weight: bold; font-size: 16px;">Coverage</div>

                                <div style="margin-bottom: 8px;">
                                    <span style="width: 140px; font-weight: bold; display: inline-block;">Items to cover:</span>
                                    <div style="margin-left: 140px;">
                                        <div>• building</div>
                                        <div>• contents</div>
                                        <div>• <input type="text" id="q7" class="answer-input" placeholder="7" style="width: 120px; display: inline-block;"></div>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <span style="width: 140px; font-weight: bold;">Quotation:</span>
                                    £ <input type="text" id="q8" class="answer-input" placeholder="8" style="width: 100px;">
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <span style="width: 140px; font-weight: bold;">Coverage start date:</span>
                                    <input type="text" id="q9" class="answer-input" placeholder="9" style="width: 120px;">
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <span style="width: 140px; font-weight: bold;">Reference number:</span>
                                    <input type="text" id="q10" class="answer-input" placeholder="10" style="width: 100px;">
                                </div>
                            </div></div>

                    </div>
                </div>
            </div>

            <div id="part-2" class="question-part hidden">
                <div class="part-header">
                    <p><strong>Part 2</strong></p>
                    <p>Listen and answer questions 11–20.</p>
                </div>
                <div class="questions-container">
                    <div class="question">
                        <div class="question-prompt">
                            <p><strong>Questions 11-14</strong><br>Choose the correct letter, <strong> A, B or C</strong>.</p>
                        </div>
                        <div class="single-choice-container">
                            <div class="single-choice">
                                <p><strong>11</strong>   The top two proposals for the design of the swimming pool were chosen by</p>
                                <label><input type="radio" name="q11" value="A">&nbsp;&nbsp;the public.</label>
                                <label><input type="radio" name="q11" value="B">&nbsp;&nbsp;the radio station.</label>
                                <label><input type="radio" name="q11" value="C">&nbsp;&nbsp;architects</label>
                            </div>
                            <div class="single-choice">
                                <p><strong>12</strong>   What is special about the pool's construction?</p>
                                <label><input type="radio" name="q12" value="A">&nbsp;&nbsp;It was constructed by the people.</label>
                                <label><input type="radio" name="q12" value="B">&nbsp;&nbsp;Its fishbowl-like shape</label>
                                <label><input type="radio" name="q12" value="C">&nbsp;&nbsp;It is the first pool in Bridgewater.</label>
                            </div>
                            <div class="single-choice">
                                <p><strong>13</strong>   News reports covering the new pool expressed concerns over</p>
                                <label><input type="radio" name="q13" value="A">&nbsp;&nbsp;price</label>
                                <label><input type="radio" name="q13" value="B">&nbsp;&nbsp;safety</label>
                                <label><input type="radio" name="q13" value="C">&nbsp;&nbsp;size</label>
                            </div>
                            <div class="single-choice">
                                <p><strong>14</strong>   What factor of the pool's Grand Opening remains undecided?</p>
                                <label><input type="radio" name="q14" value="A">&nbsp;&nbsp;who will host</label>
                                <label><input type="radio" name="q14" value="B">&nbsp;&nbsp;the exact opening time</label>
                                <label><input type="radio" name="q14" value="C">&nbsp;&nbsp;what sculpture will be in the foyer</label>
                            </div>
                        </div>
                    </div>
                    <div class="question">
                        <div class="question-prompt">
                            <p><strong>Questions 15-20</strong></p>
                            <p>What's the theme of each continent based on the rooms of the clubhouse?</p>
                            <p>Choose <strong>SIX</strong> answers from the box and write the correct letter, <strong>A-H</strong>, next to questions 15-20.</p>
                        </div>
                        <div class="matching-container">
                            <table class="matching-table">
                                <thead>
                                    <tr>
                                        <th>Continent</th>
                                        <th>Answer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>15</strong>   Asia</td>
                                        <td><div class="drop-zone" data-question-id="15" data-question="q15"></div></td>
                                    </tr>
                                    <tr>
                                        <td><strong>16</strong>   Antarctica</td>
                                        <td><div class="drop-zone" data-question-id="16" data-question="q16"></div></td>
                                    </tr>
                                    <tr>
                                        <td><strong>17</strong>   Africa</td>
                                        <td><div class="drop-zone" data-question-id="17" data-question="q17"></div></td>
                                    </tr>
                                    <tr>
                                        <td><strong>18</strong>   North America</td>
                                        <td><div class="drop-zone" data-question-id="18" data-question="q18"></div></td>
                                    </tr>
                                    <tr>
                                        <td><strong>19</strong>   Europe</td>
                                        <td><div class="drop-zone" data-question-id="19" data-question="q19"></div></td>
                                    </tr>
                                    <tr>
                                        <td><strong>20</strong>   South America</td>
                                        <td><div class="drop-zone" data-question-id="20" data-question="q20"></div></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="matching-options-list">
                                <div class="drag-item" draggable="true" data-option-id="A">film and music</div>
                                <div class="drag-item" draggable="true" data-option-id="B">mountains</div>
                                <div class="drag-item" draggable="true" data-option-id="C">space travel</div>
                                <div class="drag-item" draggable="true" data-option-id="D">jewelry</div>
                                <div class="drag-item" draggable="true" data-option-id="E">animals</div>
                                <div class="drag-item" draggable="true" data-option-id="F">waterways</div>
                                <div class="drag-item" draggable="true" data-option-id="G">volcano</div>
                                <div class="drag-item" draggable="true" data-option-id="H">ancient forts</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="part-3" class="question-part hidden">
                <div class="part-header">
                    <p><strong>Part 3</strong></p>
                    <p>Listen and answer questions 21–30.</p>
                </div>
                <div class="questions-container">
                    <div class="question">
                        <div class="question-prompt">
                            <p><strong>Questions 21-25</strong></p>
                            <p>Choose the correct letter, <strong>A, B or C</strong>.</p>
                        </div>
                        <div class="single-choice-container">
                            <div class="single-choice">
                                <p><strong>21</strong>   Which part has the tutor already read?</p>
                                <label><input type="radio" name="q21" value="A">&nbsp;&nbsp;the introductory chapter</label>
                                <label><input type="radio" name="q21" value="B">&nbsp;&nbsp;the procedure section</label>
                                <label><input type="radio" name="q21" value="C">&nbsp;&nbsp;the results and discussion section</label>
                            </div>
                            <div class="single-choice">
                                <p><strong>22</strong>   Which part of the paper did the tutor like?</p>
                                <label><input type="radio" name="q22" value="A">&nbsp;&nbsp;introduction</label>
                                <label><input type="radio" name="q22" value="B">&nbsp;&nbsp;layout</label>
                                <label><input type="radio" name="q22" value="C">&nbsp;&nbsp;background information</label>
                            </div>
                            <div class="single-choice">
                                <p><strong>23</strong>   Kathy and the tutor both agree to continue to</p>
                                <label><input type="radio" name="q23" value="A">&nbsp;&nbsp;refer a lot to the example received in class.</label>
                                <label><input type="radio" name="q23" value="B">&nbsp;&nbsp;copy the information.</label>
                                <label><input type="radio" name="q23" value="C">&nbsp;&nbsp;conduct further research in the library.</label>
                            </div>
                            <div class="single-choice">
                                <p><strong>24</strong>   Kathy asks the tutor for help with the ………….. section.</p>
                                <label><input type="radio" name="q24" value="A">&nbsp;&nbsp;abstract</label>
                                <label><input type="radio" name="q24" value="B">&nbsp;&nbsp;bibliography</label>
                                <label><input type="radio" name="q24" value="C">&nbsp;&nbsp;appendix</label>
                            </div>
                            <div class="single-choice">
                                <p><strong>25</strong>   What will Kathy do next?</p>
                                <label><input type="radio" name="q25" value="A">&nbsp;&nbsp;try out software</label>
                                <label><input type="radio" name="q25" value="B">&nbsp;&nbsp;work on the bibliography</label>
                                <label><input type="radio" name="q25" value="C">&nbsp;&nbsp;make an animation</label>
                            </div>
                        </div>
                    </div>

                    <div class="question">
                        <div class="question-prompt">
                            <p><strong>Questions 26-30</strong></p>
                            <p>What is the desired outcome to each of the following course of action?</p>
                            <p>Choose <strong>FIVE</strong> answers from the box and write the correct letter, <strong>A-F</strong>, next to questions 26-30.</p>
                        </div>
                        <div class="matching-container">
                            <table class="matching-table">
                                <thead>
                                    <tr>
                                        <th>Course of Action</th>
                                        <th>Answer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>26</strong>   Make a good grade</td>
                                        <td><div class="drop-zone" data-question-id="26" data-question="q26"></div></td>
                                    </tr>
                                    <tr>
                                        <td><strong>27</strong>   Meet engineering professionals</td>
                                        <td><div class="drop-zone" data-question-id="27" data-question="q27"></div></td>
                                    </tr>
                                    <tr>
                                        <td><strong>28</strong>   Visit the factory</td>
                                        <td><div class="drop-zone" data-question-id="28" data-question="q28"></div></td>
                                    </tr>
                                    <tr>
                                        <td><strong>29</strong>   Seek summer internships</td>
                                        <td><div class="drop-zone" data-question-id="29" data-question="q29"></div></td>
                                    </tr>
                                    <tr>
                                        <td><strong>30</strong>   Present dissertation</td>
                                        <td><div class="drop-zone" data-question-id="30" data-question="q30"></div></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="matching-options-list">
                                <div class="drag-item" draggable="true" data-option-id="A">practical experience</div>
                                <div class="drag-item" draggable="true" data-option-id="B">publish the work</div>
                                <div class="drag-item" draggable="true" data-option-id="C">join Machine Engineer Society</div>
                                <div class="drag-item" draggable="true" data-option-id="D">give suggestions</div>
                                <div class="drag-item" draggable="true" data-option-id="E">stay up to date</div>
                                <div class="drag-item" draggable="true" data-option-id="F">make important contacts</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="part-4" class="question-part hidden">
                <div class="part-header">
                    <p><strong>Part 4</strong></p>
                    <p>Listen and answer questions 31–40.</p>
                </div>
                <div class="questions-container">
                    <div class="question">
                        <div class="question-prompt">
                            <p><strong>Questions 31-40</strong></p>
                            <p>Complete the notes below.</p>
                            <p>Write <strong>NO MORE THAN TWO WORDS AND/OR A NUMBER</strong> for each answer.</p>
                        </div>
                        <div style="border: 1px solid #000000; padding: 15px;"> <p class="centered-title">An Overview of The Research on Amber</p>
                        <p>Amber: a fossilised tree resin, which may be produced to protect itself against <input type="text" class="answer-input" id="q31" placeholder="31"> and fungi.</p>
                        <p><strong>Colors:</strong></p>
                        <ul style="list-style-type: none; padding-left: 20px;">
                            <li>&bull;&nbsp;&nbsp;usual yellow, orange, or brown</li>
                            <li>&bull;&nbsp;&nbsp;uncommon colors e.g. blue (what causes the blue color in amber is related to the occurrence of <input type="text" class="answer-input" id="q32" placeholder="32">)</li>
                        </ul>
                        <p><strong>Formation</strong></p>
                        <ul style="list-style-type: none; padding-left: 20px;">
                            <li>&bull;&nbsp;&nbsp;under sustained <input type="text" class="answer-input" id="q33" placeholder="33"> and pressure</li>
                            <li>&bull;&nbsp;&nbsp;during an <input type="text" class="answer-input" id="q34" placeholder="34"> stage between resins and amber, copal is produced.</li>
                        </ul>
                        <p><strong>Places and Conditions</strong></p>
                        <ul style="list-style-type: none; padding-left: 20px;">
                            <li>&bull;&nbsp;&nbsp;commonly found on <input type="text" class="answer-input" id="q35" placeholder="35"> e.g. in Russia</li>
                            <li>&bull;&nbsp;&nbsp;avoid exposure to <input type="text" class="answer-input" id="q36" placeholder="36">, rain, and temperate extremes</li>
                        </ul>
                        <p><strong>Inclusions</strong></p>
                        <ul style="list-style-type: none; padding-left: 20px;">
                            <li>&bull;&nbsp;&nbsp;Dominican amber: 1 inclusion to every 100 pieces</li>
                            <li>&bull;&nbsp;&nbsp;Baltic amber: 1 inclusion to every <input type="text" class="answer-input" id="q37" placeholder="37"> pieces</li>
                        </ul>
                        <p><strong>Uses and Applications</strong></p>
                        <ul style="list-style-type: none; padding-left: 20px;">
                            <li>&bull;&nbsp;&nbsp;It can be used to make ornamental objects and jewelry in <input type="text" class="answer-input" id="q38" placeholder="38"> settings.</li>
                            <li>&bull;&nbsp;&nbsp;Some people believe that its powder mixed with <input type="text" class="answer-input" id="q39" placeholder="39"> cures throat, eye and ear diseases.</li>
                            <li>&bull;&nbsp;&nbsp;It has even been used as a <input type="text" class="answer-input" id="q40" placeholder="40"> material, for instance using it to create Amber Room.</li>
                        </ul></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <div id="transcription-container">
                <h2>Transcription</h2>
                <pre id="transcription-text"></pre>
            </div>
        </div>
    </div>

    <div id="transcription-data" style="display:none;">
        <div data-part="1"><strong>SECTION 1</strong>

You will hear an agent calling from Farrelly Mutual about the recent homeowners insurance inquiry. First, you have some time to look at questions 1 to 6. You will see that there is an example which has been done for you. On this occasion only, the conversation relating to this will be played first.

Hello? Yes, I'd like to speak with Janet Evans, please. Speaking. Hi Miss Evans, this is Jim Rodriguez calling from Farrelly Mutual about your recent homeowners insurance inquiry.

The man says that he'd like to speak with Janet Evans, so Janet Evans has been written in the space. Now we shall begin. You should answer the questions as you listen, because you will not hear the recording a second time.

Listen carefully and answer questions 1 to 6. Hello? Yes, I'd like to speak with Janet Evans, please. Speaking. Hi Miss Evans, this is Jim Rodriguez calling from Farrelly Mutual about your recent homeowners insurance inquiry.

Yes, hi. Thanks for returning my call. My pleasure.

I understand you are potentially interested in insurance for a bungalow located a bit out of town. Could you give me the address? Sure, it's 49 Greenway Court. Greenway is one word.

Thank you. Alright, and would you... Either one is fine. Maybe try emailing me first, and as an alternative, I can give you my phone number.

Great, and... Hmm, did you say cat as in the animal? Yes, it is the acronym for the construction company I work for. I'm sure you've seen them around. Yes, I have, and could you give me your primary phone number? Sure, the number is 020-4251-9443.

I am generally unable to answer my phone at work, but any time after 5.30pm is fine. I will make a note of that here, so we can make an as of your house. Hmm, well, I don't have the exact measurements, but I'm pretty sure it's right around 80 square metres.

Should I measure it and call you back later? No, that's completely alright. I'll write 80... Okay, great. And what material, for example, wood, brick, stucco? It's mainly brick.

Great, that will give you a lower rate than most other materials, since it is so strong. Wonderful. And do you have any sort of home security, Miss Evans? Hmm, we don't have a fence or anything yet, but we have an alarm system that we use regularly.

Good. Before you hear the rest of the conversation, you have some time to look at questions 7-10. Now listen and answer questions 7-10.

Now I'll go through a number of things you want your policy to cover. Okay. We'll start with the building itself first.

Would you like us to cover incidental damage to the structure to your house? Absolutely. Splendid. For all items, 200 pounds, would you like us to cover theft and damage beyond natural wear and tear? The second option here will come with a considerable increase in your rates.

I think I'd just like the contents of the house to be covered against theft then. All right, and would you like any of... Yes, I definitely want flood coverage. It rains a lot here and the drainage system in the area is not the greatest.

Okay, it looks like your annual insurance rate will be... Thanks, that seems somewhat reasonable. I would like to take some time to think about it. How long does it take to begin receiving coverage after signing up? If you sign up by July the 1st, you could start your coverage by August the 1st.

I see. Okay, thanks for your help. Should I call you back at this number when I have made my decision? Yes, please.

And so that we can look up your... Give you a reference number that you should provide when calling. Ready? Yep. It's T... Got it.

Thanks. Thank you and have a nice... That is the end of section 1. You now have half a minute to check your answers. Now turn to section 2.</div>
        <div data-part="2"><strong>SECTION 2</strong>

You will hear a talk about a pool and outdoor venue created by some people. First, you have some time to look at questions 11 to 14. Now, listen carefully and answer questions 11 to 14.

Hey, if you're just joining us on WKPX The Sound, welcome. We're here in the studio with Matt and Cam in the morning, and this morning we're talking about keeping the kids occupied on summer vacation. Folks, there's a new kid in town in the world of summer fun. Get ready for the Pool of the People, a pool and outdoor venue created by, that's right, the people.

Scheduled to open in November, the ideas for everything from the design of the pool right down to the items sold in the snack bar have been decided upon by a sample of 1,050 members of the public. The public selected two top proposals from over a dozen created by renowned architect Ned Mosby, and the final design is truly something else. The pool is shaped like a fish bowl, sinking down into the ground.

And there's, you guessed it, a real live fish tank in the center. It's certainly the center of attention in the Bridgewater area. Now, you are probably wondering how much an extravagance like this must cost, right? Well, have no fear.

At just £15 for adults and £10 for kids, it's an affordable way to entertain the kids in those dog days of summer. The only problem now is the possibility that it will, in fact, become too popular. The pool is only so large, so swarms of people coming to enjoy it may cause quite a crowd in its first summer of opening.

There will be an opening party for a select audience, and in line with the pool's mission, the people have decided on all the arrangements. They collectively decided on actress Rebel Wilson to host in the festivities scheduled for later this month, and even dictated the playlist by ranking their top ten songs from a list of hundreds. There is some discrepancy, however, on the sculpture design for the foyer at the entrance.

The people elected a jellyfish sculpture to greet entering visitors, but given last week's vicious attack by a box jellyfish on a local youth, coordinators fear it will bring too much fear to patrons. Before you hear the rest of the talk, you have some time to look at questions 15 to 20. Now listen and answer questions 15 to 20.

The theme of the clubhouse is set to be International Waters, with a different section representing each continent, designed by the legendary local artist Roberta Annuzzi. Representing Asia in the reception area will be a mosaic made up of prominent animals indigenous to the continent, a camel, a panda, and the Siberian white tiger, to name just a few. In the West Lounge, feel the cool icy vibes of the trans-Antarctic mountains of Antarctica.

Makes you cold just thinking about it, doesn't it? Just seeing a wall with a mural of the glacial mountains is almost enough to cool you off on a December afternoon. Almost. Why not make the trip to the pool a social studies lesson at the same time? The theme in the ladies lounge room for Africa may not be what you expected.

A safari? Drum music? The Nile River? No. Did you know that Africa was home to the first jewelry? I sure didn't. By contrast, as you may expect, North America's theme for the card room is as modern, even futuristic, as it gets.

Annuzzi created for North America a sort of absurdist print, interestingly juxtaposing the moon landing of 1969 with an abstract depiction of humans living on Mars. Seems to me like an interesting commentary on the future of space exploration. And in the men's lounge room, the ancient forts of Sparta, Rome, Greece, and other European civilizations fittingly exhibit the strength and combatant characteristics of these societies.

Finally, the cafe and breakfast room area is an enchanting round room that draws all attention to its center, where there is a strikingly realistic sculpture of a volcano. The delicious food may actually be only the second most exciting part of this room in comparison to the nine foot statue, complete with brightly colored molten lava to characterize South America. Honestly, it is like a museum visiting each room of the clubhouse.

Why not make the trip to the pool an educational one for the kids? We're going to take a quick commercial break here at WKPX, but we'll be back in ten with more on what's touted to be the summer's hottest place to beat the heat. That is the end of section two. You now have half a minute to check your answers. Now turn to section three.</div>
        <div data-part="3"><strong>SECTION 3</strong>

You will hear two students called Jimmy and Cathy talking to their tutor about the current research paper. First, you have some time to look at questions 21 to 25. Now listen carefully and answer questions 21 to 25.

Before we start, Jimmy and Cathy, thanks for coming in today to talk about your current research paper. Well, I will also give you some suggestions for your future presentation later. That's great.

Okay, I've read the introductory chapter and so far I like where you're going with your research, you two. Thanks. What did you think of the procedure section? I haven't got there yet. I'll get to that and the results and discussion section in a bit.

Oh, if you haven't read the rest, are you just saying you like the introduction? No, the layout is really well done. You have each section clearly marked and have the header and footer perfectly formatted and your title page is right on the money. A lot of students have trouble with that one.

To be honest, we did refer a lot to the example we received in class. That's good to do for spacing and layout as long as you're not also copying the information. The background information is a little sparse though.

You may want to add to it. You think so? I was more worried about whether I had enough data. You definitely need more background information.

I would think about finding some more online articles or doing more research in the campus library. That's a good idea. We can go tomorrow.

I find it too tough finding the subject matter in the online journal database. I also like being able to flip through the physical journal as opposed to trying to scroll down on a computer. Me too.

Oh, I almost forgot. I've included all of my citations in the abstract, but could you help me with the bibliography? I should be using a bibliography, right? Not an appendix. Sure, I can help with that.

Yes, for this type of scientific research paper, list all sources that you cite in the body of your paper in a bibliography. Go to the website I gave you last time to see the exact way to list each source. OK, thanks.

I'll do that. We still have a lot of things to fix up. Yeah, but there's a lot of good stuff here to work with.

So enough about the paper. How is the presentation going? Well, it's all right. I'm going to go try out the new presentation software while Jimmy's working on the bibliography.

Yeah, we are hoping to make an animation of an actual pump, but still have a lot to learn about how to do that. Before you hear the rest of the talk, you have some time to look at questions 26 to 30. Now, listen carefully and answer questions 26 to 30.

Who would have thought before we started this project that we would be able to recreate the motion of a pump? This stuff is just so interesting. So glad to hear it. Yeah, I am glad I took engineering this semester.

I would definitely like to keep up with it. You know, there's an organisation called the Machine Engineer Society. You should look into joining it.

You'd need to school well in your engineering class to qualify. But I think you can do it. Interesting.

I will definitely check it out. I would really like to get in contact with some professionals in the engineering field to find out more. I don't really know anyone in the field now, though. I think if you keep meeting people in your classes and professors, you'll be able to get in contact with some really helpful people.

Well said, Jimmy. If engineering pumps is something you both are specifically interested in, make sure you stay up to date on new developments. In fact, you could visit the local water treatment facility periodically to see what new developments are going on. That may be a good way to get some practical experience.

Well, I don't think they would let you handle any equipment by just visiting the facility. If you really want to get your hands dirty, so to speak, I would recommend instead seeking a summer internship. Wow, you have so many helpful suggestions for getting a leg up.

Now, if only you could tell me how to get my work published. Wouldn't that be nice? Well, honestly, all you really need to do is once you have a dissertation, present it. Present it often and to many audiences, and once you get feedback, adjust it. You'll get published one day.

Wow, this meeting has been truly inspiring. Thanks for your help. That is the end of section 3. You now have half a minute to check your answers. Now turn to section 4.</div>
        <div data-part="4"><strong>SECTION 4</strong>

You will hear a talk about AMBER, the formation of AMBER, and its applications in different fields. First, you have some time to look at questions 31 to 40. Now, listen carefully and answer questions 31 to 40.

Tonight I'm going to present an overview of the research on AMBER. OK, I'll start by giving a brief introduction about AMBER, then talk about the formation of AMBER, and then describe AMBER's applications in different fields. First of all, what is AMBER? AMBER is not a stone, but is ancient fossilized tree resin, which is the semi-solid amorphous organic substance secreted in pockets and canals through epithelial cells of the plant.

And why is resin produced? Although there are contrasting views as to why resin is produced, it is a plant's protection mechanism. The resin may be produced to protect the tree from disease and injury inflicted by insects and fungi. AMBER occurs in a range of different colors.

Besides the usual yellow, orange, and brown, other uncommon colors are also associated with it. Interestingly, blue amber, the rarest Dominican amber, is highly sought after. It is only found in Santiago, Dominican Republic.

There are several theories about what causes the blue color in amber. The most common one links it to the occurrence of volcanic dust that was present when the resin was first pressed out from Himnaya Protera millions of years ago. At this point, you might be curious about how amber is formed.

Molecular polymerization resulting from high pressures and temperatures produced by overlying sediment transforms the resin first into copal. Sustained heat and pressure drives off terpenes and results in the formation of amber. Copal, that I've just mentioned, is also a tree resin, but it hasn't fully fossilized to amber.

More generally, the term copal describes resinous substances in an intermediate stage of polymerization and hardening between gummier resins and amber. So where can we find amber? It can be found on seashores. The main producer, worldwide, is Russia.

In fact, about 90% of the world's available amber is located in the Kaliningrad region of Russia, which is located on the Baltic. Here, the resin is washed up on the coast after being dislodged from the ocean floor by years of water and ocean currents. However, exposure to sunlight, rain, and temperate extremes tends to disintegrate resin.

This also indicates that amber is not really an ideal fossil preservative for most uses. We've already learned that amber is made of tree resin. It often includes insects that were trapped within the tree many millions of years ago.

A piece with a visible and well-arranged insect is generally valued much higher than simple, solid amber. One Dominican amber source reported finding a butterfly with a 5-inch wingspread. This is both a large and unusual find, and most butterfly specimens have no more than a 2-inch wingspan.

Inclusions in Dominican amber are numerous, one inclusion to every 100 pieces. Baltic amber contains approximately one inclusion to every 1,000 pieces. Now that you have a basic knowledge of amber, I'd like to talk a bit about amber's application in different fields.

First, amber is appreciated for its color and beauty. Good quality amber is used to manufacture ornamental objects and jewelry. For instance, using a variety of exclusive first-class quality natural Baltic amber with silver to make natural amber jewelry.

But due to the biodegradation of amber fossils, people with amber jewelry have to take special care of it to ensure that the amber is not damaged. It was previously believed that amber worn on the neck served to protect one from diseases of the throat and preserved the sound mind. Calistrate, a famous doctor in the Roman Empire, wrote that amber powder mixed with honey cures throat, eye, and ear diseases, and if it is taken with water, eases stomachache.

While the mystery around that use of amber has not been cleared, one thing is sure, it will help effectively to defeat small malaises. Amber has even been used as a building material. Amber created the altar in St. Brigida Church in Gdansk, Poland.

In St. Petersburg, Russia, the walls of the famous amber room were lined with intricate carvings and inlaid designs. This palace room is being reconstructed from photographs and can be visited at the Catherine Palace, located in the town of Tsarkoye Selo. And finally, the fourth use of amber is that... That is the end of section 4. You now have half a minute to check your answers. That is the end of the listening test.</div>
    </div>

    <audio id="global-audio-player" class="hidden"></audio>

    <!-- Navigation Arrows -->
    <div class="nav-arrows">
        <button class="nav-arrow" onclick="previousPart()" id="prevBtn">‹</button>
        <button class="nav-arrow" onclick="nextPart()" id="nextBtn">›</button>
    </div>

    <!-- Bottom Navigation -->
    <nav class="nav-row perScorableItem" aria-label="Questions">
        <div class="footer__questionWrapper___1tZ46 multiple" role="tablist">
            <button role="tab" class="footer__questionNo___3WNct" onclick="switchToPart(1)">
                <span>
                    <span aria-hidden="true" class="section-prefix">Part </span>
                    <span class="sectionNr" aria-hidden="true">1</span>
                    <span class="attemptedCount" aria-hidden="true">0 of 10</span>
                </span>
            </button>
            <div class="footer__subquestionWrapper___9GgoP">
                <button class="subQuestion scorable-item" onclick="goToQuestion(1)"><span class="sr-only">Question 1</span><span aria-hidden="true">1</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(2)"><span class="sr-only">Question 2</span><span aria-hidden="true">2</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(3)"><span class="sr-only">Question 3</span><span aria-hidden="true">3</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(4)"><span class="sr-only">Question 4</span><span aria-hidden="true">4</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(5)"><span class="sr-only">Question 5</span><span aria-hidden="true">5</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(6)"><span class="sr-only">Question 6</span><span aria-hidden="true">6</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(7)"><span class="sr-only">Question 7</span><span aria-hidden="true">7</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(8)"><span class="sr-only">Question 8</span><span aria-hidden="true">8</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(9)"><span class="sr-only">Question 9</span><span aria-hidden="true">9</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(10)"><span class="sr-only">Question 10</span><span aria-hidden="true">10</span></button>
            </div>
        </div>

        <div class="footer__questionWrapper___1tZ46 selected multiple" role="tablist">
            <button role="tab" class="footer__questionNo___3WNct" onclick="switchToPart(2)">
                <span>
                    <span aria-hidden="true" class="section-prefix">Part </span>
                    <span class="sectionNr" aria-hidden="true">2</span>
                    <span class="attemptedCount" aria-hidden="true">0 of 10</span>
                </span>
            </button>
            <div class="footer__subquestionWrapper___9GgoP">
                <button class="subQuestion scorable-item" onclick="goToQuestion(11)"><span class="sr-only">Question 11</span><span aria-hidden="true">11</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(12)">
                    <span class="sr-only">Question 12</span>
                    <span aria-hidden="true">12</span>
                </button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(13)">
                    <span class="sr-only">Question 13</span>
                    <span aria-hidden="true">13</span>
                </button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(14)">
                    <span class="sr-only">Question 14</span>
                    <span aria-hidden="true">14</span>
                </button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(15)">
                    <span class="sr-only">Question 15</span>
                    <span aria-hidden="true">15</span>
                </button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(16)">
                    <span class="sr-only">Question 16</span>
                    <span aria-hidden="true">16</span>
                </button>
                <button class="subQuestion active scorable-item" onclick="goToQuestion(17)">
                    <span class="sr-only">Question 17</span>
                    <span aria-hidden="true">17</span>
                </button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(18)">
                    <span class="sr-only">Question 18</span>
                    <span aria-hidden="true">18</span>
                </button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(19)">
                    <span class="sr-only">Question 19</span>
                    <span aria-hidden="true">19</span>
                </button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(20)">
                    <span class="sr-only">Question 20</span>
                    <span aria-hidden="true">20</span>
                </button>
            </div>
        </div>

        <div class="footer__questionWrapper___1tZ46 multiple" role="tablist">
            <button role="tab" class="footer__questionNo___3WNct" onclick="switchToPart(3)">
                <span>
                    <span aria-hidden="true" class="section-prefix">Part </span>
                    <span class="sectionNr" aria-hidden="true">3</span>
                    <span class="attemptedCount" aria-hidden="true">0 of 10</span>
                </span>
            </button>
            <div class="footer__subquestionWrapper___9GgoP">
                <button class="subQuestion scorable-item" onclick="goToQuestion(21)"><span class="sr-only">Question 21</span><span aria-hidden="true">21</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(22)"><span class="sr-only">Question 22</span><span aria-hidden="true">22</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(23)"><span class="sr-only">Question 23</span><span aria-hidden="true">23</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(24)"><span class="sr-only">Question 24</span><span aria-hidden="true">24</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(25)"><span class="sr-only">Question 25</span><span aria-hidden="true">25</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(26)"><span class="sr-only">Question 26</span><span aria-hidden="true">26</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(27)"><span class="sr-only">Question 27</span><span aria-hidden="true">27</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(28)"><span class="sr-only">Question 28</span><span aria-hidden="true">28</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(29)"><span class="sr-only">Question 29</span><span aria-hidden="true">29</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(30)"><span class="sr-only">Question 30</span><span aria-hidden="true">30</span></button>
            </div>
        </div>

        <div class="footer__questionWrapper___1tZ46 multiple" role="tablist">
            <button role="tab" class="footer__questionNo___3WNct" onclick="switchToPart(4)">
                <span>
                    <span aria-hidden="true" class="section-prefix">Part </span>
                    <span class="sectionNr" aria-hidden="true">4</span>
                    <span class="attemptedCount" aria-hidden="true">0 of 10</span>
                </span>
            </button>
            <div class="footer__subquestionWrapper___9GgoP">
                <button class="subQuestion scorable-item" onclick="goToQuestion(31)"><span class="sr-only">Question 31</span><span aria-hidden="true">31</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(32)"><span class="sr-only">Question 32</span><span aria-hidden="true">32</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(33)"><span class="sr-only">Question 33</span><span aria-hidden="true">33</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(34)"><span class="sr-only">Question 34</span><span aria-hidden="true">34</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(35)"><span class="sr-only">Question 35</span><span aria-hidden="true">35</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(36)"><span class="sr-only">Question 36</span><span aria-hidden="true">36</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(37)"><span class="sr-only">Question 37</span><span aria-hidden="true">37</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(38)"><span class="sr-only">Question 38</span><span aria-hidden="true">38</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(39)"><span class="sr-only">Question 39</span><span aria-hidden="true">39</span></button>
                <button class="subQuestion scorable-item" onclick="goToQuestion(40)"><span class="sr-only">Question 40</span><span aria-hidden="true">40</span></button>
            </div>
        </div>

        <button id="deliver-button" aria-label="Review your answers" class="footer__deliverButton___3FM07">
            <i class="fa fa fa-check" aria-hidden="true"></i>
            <span>Check Answers</span>
        </button>
    </nav>

    <!-- Context Menu -->
    <div id="contextMenu" class="context-menu">
        <div class="context-menu-item" id="highlight-item" onclick="highlightText()">
            <span class="context-menu-icon">
                <svg viewBox="0 0 24 24" fill="black"><path d="M15.24 12.32L12.28 9.36l-1.04 1.04-1.04-1.04-1.04 1.04-1.04-1.04-1.04 1.04-1.04-1.04-3.12 3.12c-.59.58-1.54.58-2.12 0l-1.04-1.04c-.58-.59-.58-1.54 0-2.12L7.88.88c.59-.58 1.54-.58 2.12 0l1.04 1.04 1.04-1.04 1.04 1.04 1.04-1.04 1.04 1.04 1.04-1.04 3.12 3.12c.58.59.58 1.54 0 2.12l-1.04 1.04c-.58.59-1.54.59-2.12 0zM12 15l-3 3-7-7 3-3 7 7zm-9-5l-2.29 2.29c-.39.39-.39 1.02 0 1.41l8.29 8.29c.39.39 1.02.39 1.41 0L22.7 8.41c.39-.39.39-1.02 0-1.41L14.42.29c-.39-.39-1.02-.39-1.41 0L3 10z"/></svg>
            </span>
            <span>Highlight</span>
        </div>
        <div class="context-menu-item" id="comment-item" onclick="addComment()">
            <span class="context-menu-icon">
                <svg viewBox="0 0 24 24" fill="black"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 12H5.17L4 15.17V4h16v10z"/></svg>
            </span>
            <span>Comment</span>
        </div>
        <div class="context-menu-item" id="clear-item" onclick="clearHighlight()" style="display:none;">
            <span class="context-menu-icon">
                <svg viewBox="0 0 24 24" fill="black"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
            </span>
            <span>Clear</span>
        </div>
        <div class="context-menu-item" onclick="clearAllHighlights()">
            <span class="context-menu-icon">
                <svg viewBox="0 0 24 24" fill="black"><path d="M16 9h-2.29l3-3-1.42-1.42-3 3V5h-2v2.29l-3-3-1.42 1.42 3 3H5v2h2.29l-3 3 1.42 1.42 3-3V17h2v-2.29l3 3 1.42-1.42-3-3H17V9h-2zm-4 4h-2v-2h2v2z"/></svg>
            </span>
            <span>Clear All</span>
        </div>
    </div>

    <!-- Mobile highlight/comment toolbar -->
    <div id="mobile-selection-menu" class="mobile-selection-menu hidden">
        <button onclick="highlightText()">Highlight</button>
        <button onclick="addComment()">Comment</button>
    </div>

    <!-- Result Modal -->
    <div id="result-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Your Results</h2>
                <button id="modal-close-button" class="modal-close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <p id="score-summary"></p>
                <div id="result-details"></div>
            </div>
        </div>
    </div>

    <script>
        let currentPart = 1;
        let currentQuestion = 1;
        let selectedText = '';
        let selectedRange = null;
        let draggedElement = null;
        let testStarted = false;
        let timeInSeconds = 3600;
        let isHovering = false;
        let timerInterval;
        let contextElement = null;

        function updateAnsweredIndicators() {
            for (let qNum = 1; qNum <= 40; qNum++) {
                const btn = document.querySelector(`.subQuestion[onclick="goToQuestion(${qNum})"]`);
                if (!btn) continue;

                // Do not change style if it's already marked correct or incorrect
                if (btn.classList.contains('correct') || btn.classList.contains('incorrect')) {
                    continue;
                }

                let isAnswered = false;
                const textInput = document.getElementById(`q${qNum}`);
                const radioInput = document.querySelector(`input[name="q${qNum}"]:checked`);
                const dropZone = document.querySelector(`.drop-zone[data-question-id="${qNum}"]`);

                if (textInput && textInput.value.trim() !== '') {
                    isAnswered = true;
                } else if (radioInput) {
                    isAnswered = true;
                } else if (dropZone && dropZone.querySelector('.drag-item')) {
                    isAnswered = true;
                } else if ((qNum >= 17 && qNum <= 18) && document.querySelector('input[name="q17-18"]:checked')) {
                    isAnswered = true;
                } else if ((qNum >= 19 && qNum <= 20) && document.querySelector('input[name="q19-20"]:checked')) {
                    isAnswered = true;
                } else if ((qNum >= 21 && qNum <= 23) && document.querySelector('input[name="q21-23"]:checked')) {
                    isAnswered = true;
                }

                if (isAnswered) {
                    btn.classList.add('answered');
                } else {
                    btn.classList.remove('answered');
                }
            }
        }

        const audioPlayer = document.getElementById('global-audio-player');
        const timerContainer = document.querySelector('.timer-container');
        const timerDisplay = document.querySelector('.timer-display');
        const volumeSlider = document.getElementById('new-volume-slider');
        const playPauseBtn = document.getElementById('play-pause-btn');
        const progressBar = document.getElementById('progress-bar');
        const currentTimeEl = document.getElementById('current-time');
        const totalDurationEl = document.getElementById('total-duration');
        const speedBtn = document.getElementById('speed-btn');
        const speedOptions = document.getElementById('speed-options');
        const volumeBtn = document.getElementById('volume-btn');

        const audioSource = 'https://ia600901.us.archive.org/31/items/1-p-493-nfi-5-copy-copy-copy-copy-copy-copy-copy/1_P493NFI5%20-%20CopyCopyCopyCopyCopyCopyCopy.mp3';
        audioPlayer.src = audioSource;

        const correctAnswers = {
            'q1': ['greenway'],
            'q2': ['pk2@cat.com'],
            'q3': ['5.30', '5:30'],
            'q4': ['80'],
            'q5': ['brick'],
            'q6': ['alarm system'],
            'q7': ['flood'],
            'q8': ['148.30'],
            'q9': ['1 august', 'august 1', 'august 1st', '1st august'],
            'q10': ['tr278q'],
            'q11': 'A',
            'q12': 'B',
            'q13': 'C',
            'q14': 'C',
            'q15': 'E',
            'q16': 'B',
            'q17': 'D',
            'q18': 'C',
            'q19': 'H',
            'q20': 'G',
            'q21': 'A',
            'q22': 'B',
            'q23': 'C',
            'q24': 'B',
            'q25': 'A',
            'q26': 'C',
            'q27': 'F',
            'q28': 'E',
            'q29': 'A',
            'q30': 'B',
            'q31': ['insects'],
            'q32': ['volcanic dust'],
            'q33': ['heat'],
            'q34': ['intermediate'],
            'q35': ['seashores'],
            'q36': ['sunlight'],
            'q37': ['1000', '1,000'],
            'q38': ['silver'],
            'q39': ['honey'],
            'q40': ['building']
        };

        const questionTypes = {
            'q1': 'text', 'q2': 'text', 'q3': 'text', 'q4': 'text', 'q5': 'text',
            'q6': 'text', 'q7': 'text', 'q8': 'text', 'q9': 'text', 'q10': 'text',
            'q11': 'mcq', 'q12': 'mcq', 'q13': 'mcq', 'q14': 'mcq', 'q15': 'drag_drop',
            'q16': 'drag_drop', 'q17': 'drag_drop', 'q18': 'drag_drop', 'q19': 'drag_drop', 'q20': 'drag_drop',
            'q21': 'mcq', 'q22': 'mcq', 'q23': 'mcq', 'q24': 'mcq', 'q25': 'mcq', 'q26': 'drag_drop',
            'q27': 'drag_drop', 'q28': 'drag_drop', 'q29': 'drag_drop', 'q30': 'drag_drop',
            'q31': 'text', 'q32': 'text', 'q33': 'text', 'q34': 'text', 'q35': 'text',
            'q36': 'text', 'q37': 'text', 'q38': 'text', 'q39': 'text', 'q40': 'text'
        };


        function setupCheckboxLimits() {
            const checkboxGroups = [
                { name: 'q18-20', limit: 3 },
            ];

            checkboxGroups.forEach(group => {
                const checkboxes = document.querySelectorAll(`input[name="${group.name}"]`);
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('click', () => {
                        const checkedCount = document.querySelectorAll(`input[name="${group.name}"]:checked`).length;
                        if (checkedCount > group.limit) {
                            checkbox.checked = false;
                        }
                    });
                });
            });
        }

        function switchToPart(partNumber, andPlay = false) {
            if (currentPart !== partNumber) {
                const firstQuestionOfPart = (partNumber - 1) * 10 + 1;
                currentQuestion = firstQuestionOfPart;
            }

            currentPart = partNumber;

            updateNavigation();
            document.querySelectorAll('.question-part').forEach(part => part.classList.add('hidden'));
            const partToShow = document.getElementById(`part-${partNumber}`);
            if (partToShow) {
                partToShow.classList.remove('hidden');
            }

            if (document.querySelector('.main-container.results-mode')) {
                const transcriptionSource = document.querySelector(`#transcription-data [data-part="${partNumber}"]`);
                const transcriptionTarget = document.getElementById('transcription-text');
                if (transcriptionSource && transcriptionTarget) {
                    transcriptionTarget.innerHTML = transcriptionSource.innerHTML;
                }
            }

            updateNavigation();
        }

        function updateNavigation() {
            document.querySelectorAll('.footer__questionWrapper___1tZ46').forEach((wrapper, index) => {
                wrapper.classList.remove('selected');
                updateAttemptedCount(index + 1);
            });
            const currentWrapper = document.querySelectorAll('.footer__questionWrapper___1tZ46')[currentPart - 1];
            if (currentWrapper) {
                currentWrapper.classList.add('selected');
            }
            document.getElementById('prevBtn').disabled = currentQuestion === 1;
            document.getElementById('nextBtn').disabled = currentQuestion === 40;
        }

        function goToQuestion(questionNumber) {
            currentQuestion = questionNumber;
            let partNumber = 1;
            if (questionNumber > 10 && questionNumber <= 20) partNumber = 2;
            else if (questionNumber > 20 && questionNumber <= 30) partNumber = 3;
            else if (questionNumber > 30) partNumber = 4;
            if (currentPart !== partNumber) {
                switchToPart(partNumber);
            }
            document.querySelectorAll('.subQuestion').forEach(btn => btn.classList.remove('active'));
            const questionBtn = document.querySelector(`.subQuestion[onclick="goToQuestion(${questionNumber})"]`);
            if (questionBtn) questionBtn.classList.add('active');

            // Scroll to the question
            let questionElement;
            if (questionNumber >= 18 && questionNumber <= 20) {
                questionElement = document.querySelector('input[name="q18-20"]');
            } else if (questionNumber >= 17 && questionNumber <= 18) {
                questionElement = document.querySelector('input[name="q17-18"]');
            } else if (questionNumber >= 19 && questionNumber <= 20) {
                questionElement = document.querySelector('input[name="q19-20"]');
            } else {
                questionElement = document.getElementById(`q${questionNumber}`) || // text inputs
                                document.querySelector(`[data-question-id="${questionNumber}"]`) || // drag-drop
                                document.querySelector(`input[name="q${questionNumber}"]`); // radio buttons
            }

            if (questionElement) {
                const questionContainer = questionElement.closest('.question');
                if(questionContainer) {
                    questionContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    questionContainer.classList.add('flash');
                    setTimeout(() => {
                        questionContainer.classList.remove('flash');
                    }, 1000);
                }
            }
            updateNavigation();
        }

        function nextPart() {
            if (currentQuestion < 40) {
                goToQuestion(currentQuestion + 1);
            }
        }

        function previousPart() {
            if (currentQuestion > 1) {
                goToQuestion(currentQuestion - 1);
            }
        }

        function highlightText() {
            if (!selectedRange || selectedRange.collapsed) {
                closeContextMenu();
                return;
            }

            try {
                // Check if selection spans multiple elements
                const startContainer = selectedRange.startContainer;
                const endContainer = selectedRange.endContainer;

                if (startContainer === endContainer && startContainer.nodeType === Node.TEXT_NODE) {
                    // Simple case: selection within a single text node
                    const span = document.createElement('span');
                    span.className = 'highlight';
                    selectedRange.surroundContents(span);
                } else {
                    // Complex case: selection spans multiple elements
                    // Handle partial text selections and complex DOM structures

                    // First, handle partial text at the start
                    const startOffset = selectedRange.startOffset;
                    const endOffset = selectedRange.endOffset;

                    if (startContainer.nodeType === Node.TEXT_NODE && startOffset > 0) {
                        // Split the start text node if selection starts in the middle
                        const startText = startContainer.textContent;
                        const beforeText = startText.substring(0, startOffset);
                        const selectedStartText = startText.substring(startOffset);

                        const beforeNode = document.createTextNode(beforeText);
                        const selectedNode = document.createTextNode(selectedStartText);

                        const parent = startContainer.parentNode;
                        parent.insertBefore(beforeNode, startContainer);
                        parent.insertBefore(selectedNode, startContainer);
                        parent.removeChild(startContainer);

                        // Update the range to start from the new node
                        selectedRange.setStart(selectedNode, 0);
                    }

                    if (endContainer.nodeType === Node.TEXT_NODE && endOffset < endContainer.textContent.length) {
                        // Split the end text node if selection ends in the middle
                        const endText = endContainer.textContent;
                        const selectedEndText = endText.substring(0, endOffset);
                        const afterText = endText.substring(endOffset);

                        const selectedNode = document.createTextNode(selectedEndText);
                        const afterNode = document.createTextNode(afterText);

                        const parent = endContainer.parentNode;
                        parent.insertBefore(selectedNode, endContainer);
                        parent.insertBefore(afterNode, endContainer);
                        parent.removeChild(endContainer);

                        // Update the range to end at the new node
                        selectedRange.setEnd(selectedNode, selectedNode.textContent.length);
                    }

                    // Now extract the contents and highlight all text nodes
                    const fragment = selectedRange.extractContents();

                    // Create a tree walker to find all text nodes
                    const walker = document.createTreeWalker(
                        fragment,
                        NodeFilter.SHOW_TEXT,
                        {
                            acceptNode: function(node) {
                                // Accept text nodes that have content (not just whitespace)
                                return node.textContent.trim() ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_SKIP;
                            }
                        },
                        false
                    );

                    const textNodes = [];
                    let node;
                    while (node = walker.nextNode()) {
                        textNodes.push(node);
                    }

                    // Wrap each text node in a highlight span
                    textNodes.forEach(textNode => {
                        const span = document.createElement('span');
                        span.className = 'highlight';
                        const parent = textNode.parentNode;
                        if (parent) {
                            parent.insertBefore(span, textNode);
                            span.appendChild(textNode);
                        }
                    });

                    // Insert the highlighted fragment back
                    selectedRange.insertNode(fragment);
                }
            } catch(err) {
                console.warn('Primary highlighting failed, trying robust fallback:', err);

                // Robust fallback for complex selections
                try {
                    const selection = window.getSelection();
                    if (selection.rangeCount > 0) {
                        const range = selection.getRangeAt(0);

                        // Get all nodes in the selection
                        const startContainer = range.startContainer;
                        const endContainer = range.endContainer;
                        const commonAncestor = range.commonAncestorContainer;

                        // Create a temporary range to work with
                        const tempRange = document.createRange();
                        tempRange.selectNodeContents(commonAncestor);

                        // Find the start and end positions
                        const startPos = range.startOffset;
                        const endPos = range.endOffset;

                        // Extract and highlight the content
                        const contents = range.extractContents();

                        // Recursively highlight all text content
                        function highlightAllText(node) {
                            if (node.nodeType === Node.TEXT_NODE) {
                                if (node.textContent.trim()) {
                                    const span = document.createElement('span');
                                    span.className = 'highlight';
                                    span.style.backgroundColor = '#ffff00';
                                    const parent = node.parentNode;
                                    if (parent) {
                                        parent.insertBefore(span, node);
                                        span.appendChild(node);
                                    }
                                }
                            } else if (node.nodeType === Node.ELEMENT_NODE) {
                                Array.from(node.childNodes).forEach(child => {
                                    highlightAllText(child);
                                });
                            }
                        }

                        highlightAllText(contents);
                        range.insertNode(contents);
                    }
                } catch(e2) {
                    console.warn('Robust fallback failed, using final method:', e2);
                    // Final fallback - use execCommand
                    try {
                        document.execCommand('backColor', false, 'yellow');
                    } catch(e3) {
                        console.error('All highlighting methods failed:', e3);
                        // Show user feedback
                        alert('Highlighting failed for this selection. Please try selecting smaller portions of text.');
                    }
                }
            }

            window.getSelection().removeAllRanges();
            closeContextMenu();
        }

        function addComment() {
            const commentText = prompt('Enter your comment:');
            if (!commentText || !selectedRange || selectedRange.collapsed) {
                closeContextMenu();
                return;
            }
            try {
                const span = document.createElement('span');
                span.className = 'comment-highlight';
                const tooltip = document.createElement('span');
                tooltip.className = 'comment-tooltip';
                tooltip.textContent = commentText;
                span.appendChild(tooltip);
                selectedRange.surroundContents(span);
            }catch(err){
                console.warn('Complex selection – cannot comment');
            }
            window.getSelection().removeAllRanges();
            closeContextMenu();
        }

        function clearHighlight() {
            const elementToClear = contextElement; // Set by contextmenu handler
            if (!elementToClear) {
                closeContextMenu();
                return;
            }

            const highlightId = elementToClear.getAttribute('data-highlight-id');
            const elementsToClear = highlightId
                ? Array.from(document.querySelectorAll(`[data-highlight-id="${highlightId}"]`))
                : [elementToClear];

            let commonParent = null;
            if (elementsToClear.length > 0) {
                commonParent = elementsToClear[0].parentNode;
            }

            elementsToClear.forEach(element => {
                const parent = element.parentNode;
                const fragment = document.createDocumentFragment();

                while (element.firstChild) {
                    // Don't keep the tooltip for comments. Just unwrap everything.
                    if (element.firstChild.nodeType === 1 && element.firstChild.classList.contains('comment-tooltip')) {
                        element.removeChild(element.firstChild);
                    } else {
                        fragment.appendChild(element.firstChild);
                    }
                }
                parent.replaceChild(fragment, element);
            });

            if (commonParent) {
                commonParent.normalize(); // Merges adjacent text nodes
            }

            closeContextMenu();
        }

        function clearAllHighlights() {
            document.querySelectorAll('.highlight, .comment-highlight').forEach(element => {
                const parent = element.parentNode;
                const fragment = document.createDocumentFragment();

                while (element.firstChild) {
                    if (element.firstChild.nodeType === 1 && element.firstChild.classList.contains('comment-tooltip')) {
                        element.removeChild(element.firstChild);
                    } else {
                        fragment.appendChild(element.firstChild);
                    }
                }
                parent.replaceChild(fragment, element);
            });
            closeContextMenu();
        }

        function closeContextMenu() {
            document.getElementById('contextMenu').style.display = 'none';
            contextElement = null;
        }

        function updateAttemptedCount(partNumber) {
            const partContainer = document.getElementById(`part-${partNumber}`);
            if (!partContainer) return;
            const inputs = partContainer.querySelectorAll('input.answer-input:not([disabled]), input[type="radio"]:not([disabled]), input[type="checkbox"]:not([disabled])');
            let answeredCount = 0;
            const answeredGroups = {};
            inputs.forEach(input => {
                if (input.type === 'radio' || input.type === 'checkbox') {
                    if (input.checked && !answeredGroups[input.name]) {
                        answeredCount++;
                        answeredGroups[input.name] = true;
                    }
                } else {
                    if (input.value.trim() !== '') answeredCount++;
                }
            });
            const countDisplay = document.querySelector(`.footer__questionWrapper___1tZ46:nth-child(${partNumber}) .attemptedCount`);
            if (countDisplay) countDisplay.textContent = `${answeredCount} of 10`;
        }

        function checkAnswers() {
            let score = 0;
            let resultsData = [];
            document.querySelectorAll('.correct, .incorrect, .correct-answer-text').forEach(el => el.classList.contains('correct-answer-text') ? el.remove() : el.classList.remove('correct', 'incorrect'));

            Object.keys(correctAnswers).forEach(key => {
                const qNum = parseInt(key.replace('q', ''), 10);

                const correctAnswer = correctAnswers[key];
                let userAnswer = '';
                let isCorrect = false;

                const questionType = questionTypes[key];
                const btn = document.querySelector(`.subQuestion[onclick="goToQuestion(${qNum})"]`);

                switch(questionType) {
                    case 'text':
                        const element = document.getElementById(key);
                        if (element) {
                            userAnswer = element.value.trim();
                            const acceptedAnswers = Array.isArray(correctAnswer) ? correctAnswer : [String(correctAnswer)];
                            isCorrect = acceptedAnswers.some(ans => ans.toLowerCase() === userAnswer.toLowerCase());
                            element.classList.add(isCorrect ? 'correct' : 'incorrect');
                            if (!isCorrect) {
                                const correctAnswerSpan = document.createElement('span');
                                correctAnswerSpan.className = 'correct-answer-text';
                                correctAnswerSpan.textContent = `(Correct: ${acceptedAnswers.join(' / ')})`;
                                element.parentNode.insertBefore(correctAnswerSpan, element.nextSibling);
                            }
                        }
                        break;

                    case 'mcq':
                        const radioChecked = document.querySelector(`input[name="${key}"]:checked`);
                        userAnswer = radioChecked ? radioChecked.value : '';
                        isCorrect = userAnswer.toLowerCase() === correctAnswer.toLowerCase();
                        const isTable = document.querySelector(`input[name="${key}"]`) ? document.querySelector(`input[name="${key}"]`).closest('.mcq-table') : false;

                        if (radioChecked) {
                            if (isTable) {
                                // For table MCQs, mark the row
                                const rowToMark = radioChecked.closest('tr');
                                if (rowToMark) {
                                    rowToMark.classList.add(isCorrect ? 'correct' : 'incorrect');
                                }
                            } else {
                                // For single-choice MCQs, mark the label
                                const labelToMark = radioChecked.closest('label');
                                if (labelToMark) {
                                    labelToMark.classList.add(isCorrect ? 'correct' : 'incorrect');
                                }
                            }
                        }

                        if (!isCorrect) {
                            const correctRadio = document.querySelector(`input[name="${key}"][value="${correctAnswer}"]`);
                            if (correctRadio) {
                                if (isTable) {
                                    // For table MCQs, mark the correct row
                                    const correctRow = correctRadio.closest('tr');
                                    if (correctRow) {
                                        correctRow.classList.add('correct');
                                    }
                                } else {
                                    // For single-choice MCQs, mark the correct label
                                    const correctLabel = correctRadio.closest('label');
                                    if (correctLabel) {
                                        correctLabel.classList.add('correct');
                                    }
                                }
                            }
                        }
                        break;

                    case 'drag_drop':
                        const dropZone = document.querySelector(`.drop-zone[data-question-id="${qNum}"]`);
                        if (dropZone) {
                            const droppedItem = dropZone.querySelector('.drag-item');
                            if (droppedItem) {
                                // Extract the letter from the data-option-id attribute
                                userAnswer = droppedItem.getAttribute('data-option-id');
                            } else {
                                userAnswer = '';
                            }
                            isCorrect = userAnswer.toUpperCase() === correctAnswer.toUpperCase();
                            dropZone.classList.add(isCorrect ? 'correct' : 'incorrect');
                            if (!isCorrect) {
                                const correctAnswerSpan = document.createElement('span');
                                correctAnswerSpan.className = 'correct-answer-text';
                                correctAnswerSpan.textContent = `(Correct: ${correctAnswer})`;
                                dropZone.appendChild(correctAnswerSpan);
                            }
                        }
                        break;
                }

                if (isCorrect) score++;

                if (btn) {
                    btn.classList.remove('answered');
                    btn.classList.add(isCorrect ? 'correct' : 'incorrect');
                }

                resultsData.push({
                    question: qNum,
                    userAnswer: userAnswer || 'No Answer',
                    correctAnswer: Array.isArray(correctAnswer) ? correctAnswer.join(' / ') : String(correctAnswer),
                    isCorrect: isCorrect
                });
            });

            const deliverButton = document.getElementById('deliver-button');
            const band = (function(raw){
                if(raw>=39) return 9;
                if(raw>=37) return 8.5;
                if(raw>=35) return 8;
                if(raw>=32) return 7.5;
                if(raw>=30) return 7;
                if(raw>=26) return 6.5;
                if(raw>=23) return 6;
                if(raw>=18) return 5.5;
                if(raw>=16) return 5;
                if(raw>=13) return 4.5;
                if(raw>=10) return 4;
                if(raw>=8)  return 3.5;
                if(raw>=6)  return 3;
                if(raw>=4)  return 2.5;
                if(raw>=2)  return 2;
                if(raw===1) return 1.5;
                return 0;
            })(score);
            deliverButton.innerHTML = `<span>Score: ${score} / 40 (Band ${band})</span><br><span>Estimated Band: ${band}</span>`;
            deliverButton.disabled = true;
            document.querySelectorAll('.answer-input, input[type="radio"], input[type="checkbox"]').forEach(input => {
                input.disabled = true;
            });
            document.querySelectorAll('.drag-item').forEach(item => {
                item.setAttribute('draggable', 'false');
                item.style.cursor = 'default';
            });
            document.querySelectorAll('.drop-zone').forEach(zone => {
                zone.classList.remove('drag-over'); // Ensure no lingering hover styles
            });

            document.querySelector('.main-container').classList.add('results-mode');
            switchToPart(currentPart); // Show the transcription for the current part

            // Populate and show modal
            const modal = document.getElementById('result-modal');
            const scoreSummary = document.getElementById('score-summary');
            const resultDetails = document.getElementById('result-details');

            scoreSummary.textContent = `You scored ${score} out of 40 (Band ${band}).`;

            let tableHTML = '<table><tr><th>Question</th><th>Your Answer</th><th>Correct Answer</th><th>Result</th></tr>';
            resultsData.sort((a,b) => parseInt(a.question, 10) - parseInt(b.question, 10)).forEach(res => {
                tableHTML += `
                    <tr>
                        <td>${res.question}</td>
                        <td>${res.userAnswer}</td>
                        <td>${res.correctAnswer}</td>
                        <td class="${res.isCorrect ? 'result-correct' : 'result-incorrect'}">
                            ${res.isCorrect ? '✔ Correct' : '✖ Incorrect'}
                        </td>
                    </tr>
                `;
            });
            tableHTML += '</table>';
            resultDetails.innerHTML = tableHTML;

            modal.style.display = 'flex';
        }

        function startTimer() {
            timerInterval = setInterval(() => {
                timeInSeconds--;
                const minutes = Math.floor(timeInSeconds / 60);
                const seconds = timeInSeconds % 60;
                timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                if (timeInSeconds <= 0) {
                    clearInterval(timerInterval);
                    timerDisplay.textContent = "Time's up!";
                    checkAnswers();
                }
            }, 1000);
        }

        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = Math.floor(seconds % 60);
            return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
        }

        // Event Listeners
        playPauseBtn.addEventListener('click', () => {
            if (!testStarted) {
                testStarted = true; // Timer removed, skip startTimer
                switchToPart(currentPart, true);
            } else {
                if (audioPlayer.paused) {
                    // If paused, ensure the audio source is for the current part
                    if (!audioPlayer.src.includes(audioSource)) {
                        audioPlayer.src = audioSource;
                    }
                    audioPlayer.play();
                } else {
                    audioPlayer.pause();
                }
            }
        });

        audioPlayer.addEventListener('play', () => {
            playPauseBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>`;
        });

        audioPlayer.addEventListener('pause', () => {
            playPauseBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>`;
        });

        audioPlayer.addEventListener('ended', () => {
            if (currentPart < 4) {
                switchToPart(currentPart + 1, true);
            }
        });

        audioPlayer.addEventListener('loadedmetadata', () => {
            progressBar.max = audioPlayer.duration;
            totalDurationEl.textContent = formatTime(audioPlayer.duration);
        });

        audioPlayer.addEventListener('timeupdate', () => {
            progressBar.value = audioPlayer.currentTime;
            currentTimeEl.textContent = formatTime(audioPlayer.currentTime);
        });

        progressBar.addEventListener('input', () => {
            audioPlayer.currentTime = progressBar.value;
        });

        volumeSlider.addEventListener('input', (e) => audioPlayer.volume = e.target.value);

        speedBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            speedOptions.classList.toggle('hidden');
        });

        speedOptions.addEventListener('click', (e) => {
            if (e.target.dataset.speed) {
                const speed = parseFloat(e.target.dataset.speed);
                audioPlayer.playbackRate = speed;
                speedBtn.textContent = `${speed}x`;
                speedOptions.classList.add('hidden');
            }
        });

        document.addEventListener('click', () => {
            if (!speedOptions.classList.contains('hidden')) {
                speedOptions.classList.add('hidden');
            }
        });

        document.getElementById('deliver-button').addEventListener('click', checkAnswers);

        // Go To Question Widget Logic
        const gotoWidget = document.getElementById('goto-widget');
        const gotoInput = document.getElementById('goto-input');
        const gotoBtn = document.getElementById('goto-btn');

        document.addEventListener('keydown', (e) => {
            if (e.key >= '0' && e.key <= '9' && document.activeElement.tagName !== 'INPUT') {
                e.preventDefault();
                gotoWidget.classList.remove('hidden');
                gotoInput.focus();
                gotoInput.value = e.key;
            }
            if (e.key === 'Enter' && document.activeElement === gotoInput) {
                gotoBtn.click();
            }
            if (e.key === 'Escape') {
                gotoWidget.classList.add('hidden');
                gotoInput.value = '';
            }
        });

        if (gotoBtn) {
            gotoBtn.addEventListener('click', () => {
                const qNum = parseInt(gotoInput.value, 10);
                if (qNum >= 1 && qNum <= 40) {
                    goToQuestion(qNum);
                    gotoWidget.classList.add('hidden');
                    gotoInput.value = '';
                } else {
                    alert('Please enter a number between 1 and 40.');
                }
            });
        }

        // Add listener for the modal close button
        document.getElementById('modal-close-button').addEventListener('click', () => {
            document.getElementById('result-modal').style.display = 'none';
        });

        // === Tap-to-select & tap-to-drop for drag items (mobile support) ===
        document.body.addEventListener('click', function(e){
            const item = e.target.closest('.drag-item');
            if(item && item.getAttribute('draggable')){
                // Toggle selection
                document.querySelectorAll('.drag-item.selected').forEach(i=>i.classList.remove('selected'));
                item.classList.add('selected');
                draggedElement = item;
                return;
            }
            const dz = e.target.closest('.drop-zone');
            if(dz && draggedElement){
                // If already filled, move old back
                const optionsList = document.querySelector('.matching-options-list');
                if(dz.children.length>0 && dz.firstElementChild!==draggedElement){
                    optionsList.appendChild(dz.firstElementChild);
                }
                dz.appendChild(draggedElement);
                draggedElement.classList.remove('selected');
                draggedElement=null;
                updateAnsweredIndicators();
            }
        });

        // === Mobile highlight/comment toolbar ===
        const mobileToolbar = document.getElementById('mobile-selection-menu');
        function positionToolbar(){
            const sel = window.getSelection();
            if(sel.rangeCount===0 || sel.isCollapsed){
                mobileToolbar.classList.add('hidden');
                return;
            }
            const range = sel.getRangeAt(0);
            const rect = range.getBoundingClientRect();
            mobileToolbar.style.top = window.scrollY + rect.top - mobileToolbar.offsetHeight - 8 + 'px';
            mobileToolbar.style.left = window.scrollX + rect.left + 'px';
            mobileToolbar.classList.remove('hidden');
        }
        document.addEventListener('selectionchange', () => {
            const sel = window.getSelection();
            if(sel && sel.rangeCount>0 && !sel.isCollapsed){
                selectedRange = sel.getRangeAt(0);
                selectedText = sel.toString();
            }
            if('ontouchstart' in window || navigator.maxTouchPoints>0){
                setTimeout(positionToolbar, 0);
            }
        });
        document.addEventListener('click', (e)=>{
            if(!e.target.closest('#mobile-selection-menu')){
                mobileToolbar.classList.add('hidden');
            }
        });

        /* ---------------- Drag and Drop for Matching Questions ---------------- */

        (function initDragAndDrop() {
            const container = document.querySelector('.left-panel');
            if (!container) return;

            container.addEventListener('dragstart', (e) => {
                if (e.target.classList.contains('drag-item')) {
                    draggedElement = e.target;
                    setTimeout(() => {
                        e.target.classList.add('dragging');
                    }, 0);
                }
            });

            container.addEventListener('dragend', (e) => {
                if (draggedElement) {
                    draggedElement.classList.remove('dragging');
                    draggedElement = null;
                }
            });

            container.addEventListener('dragover', (e) => {
                e.preventDefault();
                const target = e.target;
                const dropZone = target.closest('.drop-zone');
                const optionsList = target.closest('.matching-options-list');

                if (dropZone || optionsList) {
                    const overElement = dropZone || optionsList;
                    document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));
                    overElement.classList.add('drag-over');
                }
            });

            container.addEventListener('dragleave', (e) => {
                const target = e.target;
                const dropZone = target.closest('.drop-zone');
                const optionsList = target.closest('.matching-options-list');
                if (dropZone || optionsList) {
                    const overElement = dropZone || optionsList;
                    overElement.classList.remove('drag-over');
                }
            });


            container.addEventListener('drop', (e) => {
                e.preventDefault();
                if (!draggedElement) return;

                const dropTarget = e.target.closest('.drop-zone, .matching-options-list');
                if (!dropTarget) {
                    draggedElement.classList.remove('dragging');
                    draggedElement = null;
                    return;
                }

                document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over'));

                const existingItem = dropTarget.matches('.drop-zone') ? dropTarget.querySelector('.drag-item') : null;
                const originalParent = draggedElement.parentNode;

                // If there's an item in the target dropzone, swap them
                if (existingItem) {
                    originalParent.appendChild(existingItem);
                }

                dropTarget.appendChild(draggedElement);

                if(dropTarget.matches('.drop-zone')) {
                    const placeholder = dropTarget.querySelector('.placeholder');
                    if (placeholder) placeholder.style.display = 'none';
                }

                draggedElement.classList.remove('dragging');
                draggedElement = null;
                updateAnsweredIndicators();
            });
        })();


        document.addEventListener('DOMContentLoaded', () => {
            switchToPart(1);
            goToQuestion(1);
            setupCheckboxLimits();

            document.body.addEventListener('contextmenu', function (e) {
                const text = window.getSelection().toString();
                if (!text) return;
                e.preventDefault();
                const menu = document.getElementById('contextMenu');
                menu.style.left = e.pageX + 'px';
                menu.style.top = e.pageY + 'px';
                menu.style.display = 'block';

                const target = e.target;
                const clearItem = document.getElementById('clear-item');
                contextElement = target.closest('.highlight, .comment-highlight');
                if(contextElement){
                    clearItem.style.display = 'block';
                } else {
                    clearItem.style.display = 'none';
                }
            });

            document.body.addEventListener('click', function(e) {
                if (e.target.closest('.context-menu')) return;
                document.getElementById('contextMenu').style.display = 'none';
            });
        });

        // Initial setup on DOM load
        document.addEventListener('DOMContentLoaded', updateAnsweredIndicators);

        document.querySelectorAll('.mcq-table td').forEach(cell => {
            cell.addEventListener('click', (e) => {
                if (e.target.tagName !== 'INPUT') {
                    const radio = cell.querySelector('input[type="radio"]');
                    if (radio && !radio.disabled) {
                        radio.checked = true;
                    }
                }
            });
        });

        document.querySelectorAll('.answer-input, input[type="radio"], input[type="checkbox"]').forEach(input => {
            input.addEventListener('input', updateAnsweredIndicators);
            input.addEventListener('change', updateAnsweredIndicators);
        });

        function initAllDragAndDrop() {
            const dragItems = document.querySelectorAll('.drag-item');
            const dropZones = document.querySelectorAll('.drop-zone');

            dragItems.forEach(item => {
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragend', handleDragEnd);
            });

            dropZones.forEach(zone => {
                zone.addEventListener('dragover', handleDragOver);
                zone.addEventListener('dragleave', handleDragLeave);
                zone.addEventListener('drop', handleDrop);
            });

            function handleDragStart(e) {
                draggedElement = e.target;
                setTimeout(() => e.target.classList.add('dragging'), 0);
            }

            function handleDragEnd(e) {
                e.target.classList.remove('dragging');
            }

            function handleDragOver(e) {
                e.preventDefault();
                const zone = e.target.closest('.drop-zone');
                if (zone) zone.classList.add('drag-over');
            }

            function handleDragLeave(e) {
                const zone = e.target.closest('.drop-zone');
                if (zone) zone.classList.remove('drag-over');
            }

            function handleDrop(e) {
                e.preventDefault();
                const targetZone = e.target.closest('.drop-zone');
                if (!targetZone || !draggedElement) return;

                targetZone.classList.remove('drag-over');

                const sourceContainer = draggedElement.parentNode;
                const itemInTargetZone = targetZone.querySelector('.drag-item');

                // Prevent dropping on self or non-droppable area inside zone
                if (e.target.classList.contains('drag-item') && e.target !== draggedElement) {
                    return; // Or handle swap explicitly here if needed
                }

                if (itemInTargetZone && itemInTargetZone !== draggedElement) {
                    // If source is an options list, move the existing item back
                    const optionsList = findParentOptionsList(targetZone);
                    if (sourceContainer.classList.contains('matching-options-list') || sourceContainer.classList.contains('map-options-list')) {
                    optionsList.appendChild(itemInTargetZone);
                    }
                    // If source is another drop zone (swapping)
                    else if (sourceContainer.classList.contains('drop-zone')) {
                        sourceContainer.appendChild(itemInTargetZone);
                    }
                }

                targetZone.appendChild(draggedElement);
                if (targetZone.querySelector('.placeholder')) {
                    targetZone.querySelector('.placeholder').style.display = 'none';
                }

                updateAnsweredIndicators();
            }

            function findParentOptionsList(element) {
                // Specific for map
                if (element.closest('#map-drag-container')) {
                    return document.getElementById('map-options-q11-15');
                }
                // Generic for matching
                const container = element.closest('.matching-container');
                if (container) {
                    return container.querySelector('.matching-options-list');
                }
                return null;
            }
        }

        let transcriptionHighlighted = false;
        function highlightAnswersInTranscription() {
            if (transcriptionHighlighted) return;

            const highlightedTranscripts = { 1: '', 2: '', 3: '', 4: '' };

            for (let i = 1; i <= 4; i++) {
                const partElement = document.querySelector(`#transcription-data [data-part="${i}"]`);
                if (partElement) highlightedTranscripts[i] = partElement.innerHTML;
            }

            for (const key in correctAnswers) {
                const answers = Array.isArray(correctAnswers[key]) ? correctAnswers[key] : [correctAnswers[key]];
                if (answers.every(answer => /^[A-G]$/.test(answer))) {
                    continue;
                }

                const qNum = parseInt(key.match(/\d+/)[0]);
                let partNumber = 1;
                if (qNum > 10 && qNum <= 20) partNumber = 2;
                else if (qNum > 20 && qNum <= 30) partNumber = 3;
                else if (qNum > 30) partNumber = 4;

                if (!highlightedTranscripts[partNumber]) continue;

                let answersToHighlight = correctAnswers[key];
                if (!Array.isArray(answersToHighlight)) {
                    answersToHighlight = [answersToHighlight];
                }

                for (const answer of answersToHighlight) {
                    if (/^[A-G]$/.test(answer)) continue;

                    let searchTerm = answer.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                    searchTerm = searchTerm.replace(/\\\(s\\\)/g, '(s)?');

                    const regex = new RegExp(`\\b(${searchTerm})\\b`, 'gi');

                    highlightedTranscripts[partNumber] = highlightedTranscripts[partNumber].replace(
                        regex,
                        '<span class="highlight">$1</span>'
                    );
                }
            }

            for (let i = 1; i <= 4; i++) {
                const partElement = document.querySelector(`#transcription-data [data-part="${i}"]`);
                if(partElement) partElement.innerHTML = highlightedTranscripts[i];
            }
            transcriptionHighlighted = true;
        }

        function closeContextMenu() {
            document.getElementById('contextMenu').style.display = 'none';
            contextElement = null;
        }
    </script>
</body>
</html>
