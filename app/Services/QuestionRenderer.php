<?php

namespace App\Services;

use App\Models\Question;

class QuestionRenderer
{
    /**
     * Render a question based on its format for IELTS Listening
     * 
     * @param Question $question
     * @param int $questionNumber
     * @param array $userAnswers
     * @param bool $isAdmin - for admin/teacher preview
     * @return string
     */
    public static function render(Question $question, int $questionNumber, $userAnswers = [], bool $isAdmin = false)
    {
        // Get user answer for this question if it exists
        $userAnswer = $userAnswers[$question->id] ?? null;
        
        // Render based on IELTS question format
        switch ($question->question_format) {
            case 'gap_filling':
                return self::renderGapFilling($question, $questionNumber, $userAnswer, $isAdmin);
                
            case 'multiple_choice':
                return self::renderMultipleChoice($question, $questionNumber, $userAnswer, $isAdmin);
                
            case 'matching':
                return self::renderMatching($question, $questionNumber, $userAnswer, $isAdmin);
                
            case 'map_labeling':
                return self::renderMapLabeling($question, $questionNumber, $userAnswer, $isAdmin);
                
            case 'classification':
                return self::renderClassification($question, $questionNumber, $userAnswer, $isAdmin);
                
            case 'flow_chart':
                return self::renderFlowChart($question, $questionNumber, $userAnswer, $isAdmin);
                
            case 'table_completion':
                return self::renderTableCompletion($question, $questionNumber, $userAnswer, $isAdmin);
                
            case 'note_completion':
                return self::renderNoteCompletion($question, $questionNumber, $userAnswer, $isAdmin);
                
            case 'sentence_completion':
                return self::renderSentenceCompletion($question, $questionNumber, $userAnswer, $isAdmin);
                
            case 'summary_completion':
                return self::renderSummaryCompletion($question, $questionNumber, $userAnswer, $isAdmin);
                
            case 'short_answer':
                return self::renderShortAnswer($question, $questionNumber, $userAnswer, $isAdmin);
                
            default:
                return self::renderDefault($question, $questionNumber, $userAnswer, $isAdmin);
        }
    }

    /**
     * IELTS Gap Filling (Form Completion) - Part 1
     */
    private static function renderGapFilling(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $gaps = json_decode($question->gaps_data, true) ?? [];
        $userAnswers = is_array($userAnswer) ? $userAnswer : [];
        
        $html = '<div class="ielts-question gap-filling" data-question-id="' . $question->id . '">';
        
        // Question header
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">' . $questionNumber . '</span>';
        $html .= '<div class="word-limit-hint">' . ($question->word_limit ?? 'ONE WORD AND/OR A NUMBER') . '</div>';
        $html .= '</div>';
        
        // Context text
        if ($question->context_text) {
            $html .= '<div class="context-text">' . nl2br(htmlspecialchars($question->context_text)) . '</div>';
        }
        
        // Question text with form fields
        $html .= '<div class="question-content">';
        $html .= '<h4>' . htmlspecialchars($question->question_text) . '</h4>';
        
        // Form fields
        $html .= '<div class="form-fields">';
        foreach ($gaps as $index => $gap) {
            $gapNumber = $gap['number'] ?? ($questionNumber + $index);
            $currentAnswer = $userAnswers[$index] ?? '';
            
            $html .= '<div class="form-field">';
            $html .= '<label>' . htmlspecialchars($gap['label'] ?? $gap['text']) . '</label>';
            $html .= '<div class="input-with-number">';
            $html .= '<span class="field-number">' . $gapNumber . '</span>';
            $html .= '<input type="text" ';
            $html .= 'name="answers[' . $question->id . '][' . $index . ']" ';
            $html .= 'value="' . htmlspecialchars($currentAnswer) . '" ';
            $html .= 'class="gap-input" ';
            $html .= 'placeholder="' . ($question->word_limit ?? 'ONE WORD AND/OR A NUMBER') . '" ';
            $html .= 'data-question-id="' . $question->id . '" ';
            $html .= 'data-gap-index="' . $index . '">';
            $html .= '</div>';
            $html .= '</div>';
        }
        $html .= '</div>'; // form-fields
        $html .= '</div>'; // question-content
        
        // Admin preview
        if ($isAdmin && $question->acceptable_answers) {
            $html .= '<div class="admin-answers">';
            $html .= '<strong>Acceptable answers:</strong> ';
            $html .= htmlspecialchars(json_encode($question->acceptable_answers));
            $html .= '</div>';
        }
        
        $html .= '</div>'; // ielts-question
        
        return $html;
    }

    /**
     * Multiple Choice - All Parts
     */
    private static function renderMultipleChoice(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $options = json_decode($question->options, true) ?? [];
        
        $html = '<div class="ielts-question multiple-choice" data-question-id="' . $question->id . '">';
        
        // Question header
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">' . $questionNumber . '</span>';
        $html .= '<div class="instruction">Choose the correct letter, A, B, or C.</div>';
        $html .= '</div>';
        
        // Question text
        $html .= '<div class="question-text">' . nl2br(htmlspecialchars($question->question_text)) . '</div>';
        
        // Options
        $html .= '<div class="options">';
        $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
        foreach ($options as $index => $option) {
            $letter = $letters[$index] ?? ($index + 1);
            $checked = ($userAnswer === $letter) ? 'checked' : '';
            
            $html .= '<label class="option">';
            $html .= '<input type="radio" ';
            $html .= 'name="answers[' . $question->id . ']" ';
            $html .= 'value="' . $letter . '" ';
            $html .= 'class="option-input" ';
            $html .= 'data-question-id="' . $question->id . '" ';
            $html .= $checked . '>';
            $html .= '<span class="option-letter">' . $letter . '</span>';
            $html .= '<span class="option-text">' . htmlspecialchars($option) . '</span>';
            $html .= '</label>';
        }
        $html .= '</div>';
        
        if ($isAdmin) {
            $html .= '<div class="admin-answers"><strong>Correct:</strong> ' . htmlspecialchars($question->correct_answer) . '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Matching - Part 2
     */
    private static function renderMatching(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $matchingItems = json_decode($question->matching_items, true) ?? [];
        $matchingCategories = json_decode($question->matching_categories, true) ?? [];
        $userAnswers = is_array($userAnswer) ? $userAnswer : json_decode($userAnswer, true) ?? [];
        
        $html = '<div class="ielts-question matching" data-question-id="' . $question->id . '">';
        
        // Header
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">Questions ' . $questionNumber . '-' . ($questionNumber + count($matchingItems) - 1) . '</span>';
        $html .= '<div class="instruction">Choose the correct answer and move it into the gap.</div>';
        $html .= '</div>';
        
        // Question text
        $html .= '<div class="question-text">' . nl2br(htmlspecialchars($question->question_text)) . '</div>';
        
        // Matching interface
        $html .= '<div class="matching-container">';
        
        // Left side - Items to match
        $html .= '<div class="matching-items">';
        $html .= '<h4>People/Items</h4>';
        foreach ($matchingItems as $index => $item) {
            $html .= '<div class="matching-item" data-item-id="' . $index . '">';
            $html .= '<span class="item-number">' . ($questionNumber + $index) . '</span>';
            $html .= '<span class="item-text">' . htmlspecialchars($item['text']) . '</span>';
            $html .= '</div>';
        }
        $html .= '</div>';
        
        // Right side - Categories
        $html .= '<div class="matching-categories">';
        $html .= '<h4>Responsibilities</h4>';
        foreach ($matchingCategories as $catIndex => $category) {
            $html .= '<div class="matching-category drag-drop-zone" ';
            $html .= 'data-category-id="' . $catIndex . '" ';
            $html .= 'data-question-id="' . $question->id . '">';
            $html .= '<div class="category-label">' . htmlspecialchars($category['text']) . '</div>';
            $html .= '<div class="drop-zone" data-placeholder="Drop here"></div>';
            $html .= '</div>';
        }
        $html .= '</div>';
        
        $html .= '</div>'; // matching-container
        
        // Hidden inputs for answers
        foreach ($matchingItems as $index => $item) {
            $value = $userAnswers[$index] ?? '';
            $html .= '<input type="hidden" name="answers[' . $question->id . '][' . $index . ']" value="' . htmlspecialchars($value) . '">';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Map Labeling - Part 2
     */
    private static function renderMapLabeling(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $mapData = json_decode($question->map_data, true) ?? [];
        $mapLabels = $mapData['labels'] ?? [];
        $mapAreas = $mapData['areas'] ?? [];
        $userAnswers = is_array($userAnswer) ? $userAnswer : json_decode($userAnswer, true) ?? [];
        
        $html = '<div class="ielts-question map-labeling" data-question-id="' . $question->id . '">';
        
        // Header
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">Questions ' . $questionNumber . '-' . ($questionNumber + count($mapAreas) - 1) . '</span>';
        $html .= '<div class="instruction">Label the map. Choose the correct answer and move it into the gap.</div>';
        $html .= '</div>';
        
        // Question text
        $html .= '<div class="question-text">' . nl2br(htmlspecialchars($question->question_text)) . '</div>';
        
        // Map container
        $html .= '<div class="map-container">';
        
        // Map image with clickable areas
        if (isset($mapData['image'])) {
            $html .= '<div class="map-image-container">';
            $html .= '<img src="' . asset($mapData['image']) . '" alt="Map for labeling" class="map-image">';
            
            // Clickable areas
            foreach ($mapAreas as $index => $area) {
                $currentAnswer = $userAnswers[$index] ?? '';
                $html .= '<div class="map-area clickable-area" ';
                $html .= 'style="left: ' . $area['x'] . '%; top: ' . $area['y'] . '%;" ';
                $html .= 'data-area-id="' . $index . '" ';
                $html .= 'data-question-id="' . $question->id . '">';
                $html .= '<span class="area-number">' . ($questionNumber + $index) . '</span>';
                if ($currentAnswer) {
                    $html .= '<div class="area-answer">' . htmlspecialchars($currentAnswer) . '</div>';
                }
                $html .= '</div>';
            }
            
            $html .= '</div>';
        }
        
        // Available labels
        $html .= '<div class="map-labels">';
        $html .= '<h4>Available Labels</h4>';
        $html .= '<div class="labels-grid">';
        foreach ($mapLabels as $label) {
            $html .= '<div class="map-label draggable-label" draggable="true" data-label="' . htmlspecialchars($label) . '">';
            $html .= htmlspecialchars($label);
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '</div>'; // map-container
        
        // Hidden inputs
        foreach ($mapAreas as $index => $area) {
            $value = $userAnswers[$index] ?? '';
            $html .= '<input type="hidden" name="answers[' . $question->id . '][' . $index . ']" value="' . htmlspecialchars($value) . '">';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Classification - Part 3
     */
    private static function renderClassification(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $classificationItems = json_decode($question->classification_items, true) ?? [];
        $categories = json_decode($question->classification_categories, true) ?? [];
        $userAnswers = is_array($userAnswer) ? $userAnswer : json_decode($userAnswer, true) ?? [];
        
        $html = '<div class="ielts-question classification" data-question-id="' . $question->id . '">';
        
        // Header
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">Questions ' . $questionNumber . '-' . ($questionNumber + count($classificationItems) - 1) . '</span>';
        $html .= '<div class="instruction">Choose the correct answer for each fossil category and move it into the gap.</div>';
        $html .= '</div>';
        
        // Question text
        $html .= '<div class="question-text">' . nl2br(htmlspecialchars($question->question_text)) . '</div>';
        
        // Classification interface
        $html .= '<div class="classification-container">';
        
        // Left side - Features to classify
        $html .= '<div class="features-list">';
        $html .= '<h4>Features</h4>';
        foreach ($classificationItems as $index => $item) {
            $html .= '<div class="classification-item">';
            $html .= '<span class="item-number">' . ($questionNumber + $index) . '</span>';
            $html .= '<span class="item-text">' . htmlspecialchars($item['text']) . '</span>';
            $html .= '</div>';
        }
        $html .= '</div>';
        
        // Right side - Categories
        $html .= '<div class="categories-list">';
        $html .= '<h4>Categories</h4>';
        foreach ($categories as $catIndex => $category) {
            $html .= '<div class="classification-category">';
            $html .= '<div class="category-header">' . htmlspecialchars($category['text']) . '</div>';
            $html .= '<div class="category-features drop-zone" ';
            $html .= 'data-category-id="' . $catIndex . '" ';
            $html .= 'data-question-id="' . $question->id . '"></div>';
            $html .= '</div>';
        }
        $html .= '</div>';
        
        $html .= '</div>'; // classification-container
        
        // Hidden inputs
        foreach ($classificationItems as $index => $item) {
            $value = $userAnswers[$index] ?? '';
            $html .= '<input type="hidden" name="answers[' . $question->id . '][' . $index . ']" value="' . htmlspecialchars($value) . '">';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Flow Chart - Part 3
     */
    private static function renderFlowChart(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $flowSteps = json_decode($question->flow_steps, true) ?? [];
        $userAnswers = is_array($userAnswer) ? $userAnswer : [];
        
        $html = '<div class="ielts-question flow-chart" data-question-id="' . $question->id . '">';
        
        // Header
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">Questions ' . $questionNumber . '-' . ($questionNumber + count(array_filter($flowSteps, function($step) { return $step['type'] === 'input'; })) - 1) . '</span>';
        $html .= '<div class="instruction">Complete the flow-chart. Choose the correct answer and move it into the gap.</div>';
        $html .= '</div>';
        
        // Question text
        $html .= '<div class="question-text">' . nl2br(htmlspecialchars($question->question_text)) . '</div>';
        
        // Flow chart
        $html .= '<div class="flow-chart-container">';
        $inputIndex = 0;
        
        foreach ($flowSteps as $stepIndex => $step) {
            $html .= '<div class="flow-step">';
            
            if ($step['type'] === 'text') {
                $html .= '<div class="flow-text">' . htmlspecialchars($step['content']) . '</div>';
            } elseif ($step['type'] === 'input') {
                $currentAnswer = $userAnswers[$inputIndex] ?? '';
                $html .= '<div class="flow-input-container">';
                $html .= '<span class="input-number">' . ($questionNumber + $inputIndex) . '</span>';
                $html .= '<input type="text" ';
                $html .= 'name="answers[' . $question->id . '][' . $inputIndex . ']" ';
                $html .= 'value="' . htmlspecialchars($currentAnswer) . '" ';
                $html .= 'class="flow-input" ';
                $html .= 'placeholder="' . ($step['placeholder'] ?? 'Answer') . '" ';
                $html .= 'data-question-id="' . $question->id . '" ';
                $html .= 'data-input-index="' . $inputIndex . '">';
                $html .= '</div>';
                $inputIndex++;
            }
            
            // Arrow (except for last step)
            if ($stepIndex < count($flowSteps) - 1) {
                $html .= '<div class="flow-arrow">↓</div>';
            }
            
            $html .= '</div>';
        }
        
        $html .= '</div>'; // flow-chart-container
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Table Completion - Part 4
     */
    private static function renderTableCompletion(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $tableStructure = json_decode($question->table_structure, true) ?? [];
        $userAnswers = is_array($userAnswer) ? $userAnswer : [];
        
        $html = '<div class="ielts-question table-completion" data-question-id="' . $question->id . '">';
        
        // Header
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">Questions ' . $questionNumber . '-' . ($questionNumber + count(array_filter(array_flatten($tableStructure), function($cell) { return isset($cell['type']) && $cell['type'] === 'input'; })) - 1) . '</span>';
        $html .= '<div class="instruction">Complete the table. Write ONE WORD ONLY for each answer.</div>';
        $html .= '</div>';
        
        // Question text
        $html .= '<div class="question-text">' . nl2br(htmlspecialchars($question->question_text)) . '</div>';
        
        // Table
        $html .= '<table class="completion-table">';
        $inputIndex = 0;
        
        foreach ($tableStructure as $rowIndex => $row) {
            $html .= '<tr class="' . ($rowIndex === 0 ? 'header-row' : 'data-row') . '">';
            
            foreach ($row as $colIndex => $cell) {
                $html .= '<td class="table-cell">';
                
                if ($cell['type'] === 'text') {
                    $html .= '<span class="cell-text">' . htmlspecialchars($cell['content']) . '</span>';
                } elseif ($cell['type'] === 'input') {
                    $currentAnswer = $userAnswers[$inputIndex] ?? '';
                    $html .= '<div class="table-input-container">';
                    $html .= '<span class="input-number">' . ($questionNumber + $inputIndex) . '</span>';
                    $html .= '<input type="text" ';
                    $html .= 'name="answers[' . $question->id . '][' . $inputIndex . ']" ';
                    $html .= 'value="' . htmlspecialchars($currentAnswer) . '" ';
                    $html .= 'class="table-input" ';
                    $html .= 'placeholder="ONE WORD ONLY" ';
                    $html .= 'data-question-id="' . $question->id . '" ';
                    $html .= 'data-input-index="' . $inputIndex . '">';
                    $html .= '</div>';
                    $inputIndex++;
                }
                
                $html .= '</td>';
            }
            
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Note/Sentence/Summary Completion
     */
    private static function renderNoteCompletion(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        return self::renderTextWithBlanks($question, $questionNumber, $userAnswer, 'note-completion', $isAdmin);
    }

    private static function renderSentenceCompletion(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        return self::renderTextWithBlanks($question, $questionNumber, $userAnswer, 'sentence-completion', $isAdmin);
    }

    private static function renderSummaryCompletion(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        return self::renderTextWithBlanks($question, $questionNumber, $userAnswer, 'summary-completion', $isAdmin);
    }

    /**
     * Generic text with blanks renderer
     */
    private static function renderTextWithBlanks(Question $question, int $questionNumber, $userAnswer, string $type, bool $isAdmin = false)
    {
        $userAnswers = is_array($userAnswer) ? $userAnswer : [];
        
        $html = '<div class="ielts-question ' . $type . '" data-question-id="' . $question->id . '">';
        
        // Header
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">' . $questionNumber . '</span>';
        $html .= '<div class="word-limit">' . ($question->word_limit ?? 'ONE WORD ONLY') . '</div>';
        $html .= '</div>';
        
        // Question text with blanks
        $questionText = $question->question_text;
        $blankIndex = 0;
        
        $pattern = '/\[blank\]|_{3,}|\[\s*_+\s*\]|\(\s*' . $questionNumber . '\s*\)/i';
        
        $html .= '<div class="text-with-blanks">';
        $html .= preg_replace_callback($pattern, function($matches) use ($question, &$blankIndex, $userAnswers, $questionNumber) {
            $currentAnswer = $userAnswers[$blankIndex] ?? '';
            $inputNumber = $questionNumber + $blankIndex;
            $blankIndex++;
            
            return '<span class="blank-container">' .
                   '<span class="blank-number">' . $inputNumber . '</span>' .
                   '<input type="text" ' .
                   'name="answers[' . $question->id . '][' . ($blankIndex-1) . ']" ' .
                   'value="' . htmlspecialchars($currentAnswer) . '" ' .
                   'class="blank-input" ' .
                   'data-question-id="' . $question->id . '" ' .
                   'data-blank-index="' . ($blankIndex-1) . '">' .
                   '</span>';
        }, $questionText);
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Short Answer Questions
     */
    private static function renderShortAnswer(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $html = '<div class="ielts-question short-answer" data-question-id="' . $question->id . '">';
        
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">' . $questionNumber . '</span>';
        $html .= '<div class="instruction">Answer the questions below. Write NO MORE THAN THREE WORDS AND/OR A NUMBER for each answer.</div>';
        $html .= '</div>';
        
        $html .= '<div class="question-text">' . nl2br(htmlspecialchars($question->question_text)) . '</div>';
        
        $html .= '<div class="answer-container">';
        $html .= '<input type="text" ';
        $html .= 'name="answers[' . $question->id . ']" ';
        $html .= 'value="' . htmlspecialchars($userAnswer ?? '') . '" ';
        $html .= 'class="short-answer-input" ';
        $html .= 'placeholder="NO MORE THAN THREE WORDS AND/OR A NUMBER" ';
        $html .= 'data-question-id="' . $question->id . '">';
        $html .= '</div>';
        
        if ($isAdmin) {
            $html .= '<div class="admin-answers"><strong>Acceptable answers:</strong> ' . htmlspecialchars(json_encode($question->acceptable_answers)) . '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Default/Fallback renderer
     */
    private static function renderDefault(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $html = '<div class="ielts-question default" data-question-id="' . $question->id . '">';
        
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">' . $questionNumber . '</span>';
        $html .= '</div>';
        
        $html .= '<div class="question-text">' . nl2br(htmlspecialchars($question->question_text)) . '</div>';
        
        $html .= '<div class="answer-container">';
        $html .= '<input type="text" ';
        $html .= 'name="answers[' . $question->id . ']" ';
        $html .= 'value="' . htmlspecialchars($userAnswer ?? '') . '" ';
        $html .= 'class="default-input" ';
        $html .= 'data-question-id="' . $question->id . '">';
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Render question for admin/teacher form creation
     */
    public static function renderForAdmin(Question $question, int $index)
    {
        $formats = [
            'gap_filling' => 'Gap Filling (Form Completion)',
            'multiple_choice' => 'Multiple Choice',
            'matching' => 'Matching',
            'map_labeling' => 'Map Labeling',
            'classification' => 'Classification',
            'flow_chart' => 'Flow Chart',
            'table_completion' => 'Table Completion',
            'note_completion' => 'Note Completion',
            'sentence_completion' => 'Sentence Completion',
            'summary_completion' => 'Summary Completion',
            'short_answer' => 'Short Answer'
        ];

        $html = '<div class="admin-question-form" data-question-index="' . $index . '">';
        $html .= '<h4>Question ' . ($index + 1) . '</h4>';
        
        // Question format selector
        $html .= '<div class="form-group">';
        $html .= '<label>Question Format:</label>';
        $html .= '<select name="questions[' . $index . '][question_format]" class="question-format-selector">';
        foreach ($formats as $value => $label) {
            $selected = ($question->question_format === $value) ? 'selected' : '';
            $html .= '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
        
        // Part number
        $html .= '<div class="form-group">';
        $html .= '<label>Part:</label>';
        $html .= '<select name="questions[' . $index . '][part_number]">';
        for ($i = 1; $i <= 4; $i++) {
            $selected = ($question->part_number === $i) ? 'selected' : '';
            $html .= '<option value="' . $i . '" ' . $selected . '>Part ' . $i . '</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
        
        // Question text
        $html .= '<div class="form-group">';
        $html .= '<label>Question Text:</label>';
        $html .= '<textarea name="questions[' . $index . '][question_text]" rows="3">' . htmlspecialchars($question->question_text ?? '') . '</textarea>';
        $html .= '</div>';
        
        // Context text
        $html .= '<div class="form-group">';
        $html .= '<label>Context Text (optional):</label>';
        $html .= '<textarea name="questions[' . $index . '][context_text]" rows="2">' . htmlspecialchars($question->context_text ?? '') . '</textarea>';
        $html .= '</div>';
        
        // Word limit
        $html .= '<div class="form-group">';
        $html .= '<label>Word Limit:</label>';
        $html .= '<input type="text" name="questions[' . $index . '][word_limit]" value="' . htmlspecialchars($question->word_limit ?? 'ONE WORD AND/OR A NUMBER') . '">';
        $html .= '</div>';
        
        // Acceptable answers
        $html .= '<div class="form-group">';
        $html .= '<label>Acceptable Answers (JSON):</label>';
        $html .= '<textarea name="questions[' . $index . '][acceptable_answers]" rows="2">' . htmlspecialchars(json_encode($question->acceptable_answers ?? [])) . '</textarea>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }
}