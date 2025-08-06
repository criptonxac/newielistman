<?php

namespace App\Services;

use App\Models\Question;

class QuestionRenderer
{
    /**
     * Render a question based on its type - only 5 supported types
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
        
        // Render based on question type - only 5 supported types
        switch ($question->question_type ?? $question->type) {
            case 'multiple_choice':
                return self::renderMultipleChoice($question, $questionNumber, $userAnswer, $isAdmin);
                
            case 'fill_blank':
                return self::renderFillBlank($question, $questionNumber, $userAnswer, $isAdmin);
                
            case 'true_false':
                return self::renderTrueFalse($question, $questionNumber, $userAnswer, $isAdmin);
                
            case 'drag_drop':
                return self::renderDragDrop($question, $questionNumber, $userAnswer, $isAdmin);
                
            case 'essay':
                return self::renderEssay($question, $questionNumber, $userAnswer, $isAdmin);
                
            default:
                return self::renderDefault($question, $questionNumber, $userAnswer, $isAdmin);
        }
    }

    /**
     * Multiple Choice Questions
     */
    private static function renderMultipleChoice(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $options = json_decode($question->options, true) ?? [];
        
        $html = '<div class="ielts-question multiple-choice" data-question-id="' . $question->id . '">';
        
        // Question header
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">' . $questionNumber . '</span>';
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
     * Fill in the Blank Questions
     */
    private static function renderFillBlank(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        // Check if it's form completion type
        if ($question->question_format === 'form_completion' && $question->form_structure) {
            return self::renderFormCompletion($question, $questionNumber, $userAnswer, $isAdmin);
        }
        
        // Check if it's passage fill type
        if ($question->question_format === 'passage_fill') {
            return self::renderPassageFill($question, $questionNumber, $userAnswer, $isAdmin);
        }
        
        // Default simple fill blank
        return self::renderSimpleFillBlank($question, $questionNumber, $userAnswer, $isAdmin);
    }
    
    /**
     * Form Completion (like Home Insurance Quotation Form)
     */
    private static function renderFormCompletion(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $html = '<div class="ielts-question form-completion" data-question-id="' . $question->id . '">';
        
        // Question header
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">Questions ' . $questionNumber . '</span>';
        $html .= '</div>';
        
        // Instructions
        $html .= '<div class="question-instructions">';
        $html .= '<p>Complete the form below.</p>';
        $html .= '<p><strong>Write NO MORE THAN TWO WORDS AND/OR A NUMBER for each answer.</strong></p>';
        $html .= '</div>';
        
        // Question text (form title)
        $html .= '<div class="form-title">' . htmlspecialchars($question->question_text) . '</div>';
        
        // Form structure
        $formStructure = is_array($question->form_structure) ? $question->form_structure : json_decode($question->form_structure, true);
        
        if ($formStructure && isset($formStructure['fields'])) {
            $html .= '<div class="form-container">';
            
            foreach ($formStructure['fields'] as $field) {
                $html .= '<div class="form-row">';
                $html .= '<span class="form-label">' . htmlspecialchars($field['label']) . '</span>';
                
                if ($field['type'] === 'input') {
                    $fieldAnswer = is_array($userAnswer) ? ($userAnswer[$field['number']] ?? '') : '';
                    $html .= '<input type="text" ';
                    $html .= 'name="answers[' . $question->id . '][' . $field['number'] . ']" ';
                    $html .= 'value="' . htmlspecialchars($fieldAnswer) . '" ';
                    $html .= 'class="form-input" ';
                    $html .= 'placeholder="' . $field['number'] . '" ';
                    $html .= 'data-question-id="' . $question->id . '" ';
                    $html .= 'data-field-number="' . $field['number'] . '">';
                }
                
                if (isset($field['suffix'])) {
                    $html .= '<span class="form-suffix">' . htmlspecialchars($field['suffix']) . '</span>';
                }
                
                $html .= '</div>';
            }
            
            $html .= '</div>';
        } else {
            // Fallback to simple structure
            $html .= '<div class="form-container">';
            $html .= '<div class="form-row">';
            $html .= '<span class="form-label">Name:</span>';
            $html .= '<input type="text" name="answers[' . $question->id . ']" value="' . htmlspecialchars($userAnswer ?? '') . '" class="form-input" placeholder="1">';
            $html .= '<span class="form-suffix">Court</span>';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        if ($isAdmin) {
            $html .= '<div class="admin-answers"><strong>Correct:</strong> ' . htmlspecialchars($question->correct_answer) . '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Passage Fill (text with gaps)
     */
    private static function renderPassageFill(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $html = '<div class="ielts-question passage-fill" data-question-id="' . $question->id . '">';
        
        // Question header
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">Questions ' . $questionNumber . '</span>';
        $html .= '</div>';
        
        // Instructions
        $html .= '<div class="question-instructions">';
        $html .= '<p>Complete the form below.</p>';
        $html .= '<p><strong>Write NO MORE THAN TWO WORDS AND/OR A NUMBER for each answer.</strong></p>';
        $html .= '</div>';
        
        // Passage with gaps
        $questionText = $question->question_text;
        
        // Replace numbered placeholders with input fields
        $questionText = preg_replace_callback('/\{(\d+)\}/', function($matches) use ($question, $userAnswer) {
            $number = $matches[1];
            $fieldAnswer = is_array($userAnswer) ? ($userAnswer[$number] ?? '') : '';
            
            return '<input type="text" ' .
                   'name="answers[' . $question->id . '][' . $number . ']" ' .
                   'value="' . htmlspecialchars($fieldAnswer) . '" ' .
                   'class="passage-input" ' .
                   'placeholder="' . $number . '" ' .
                   'data-question-id="' . $question->id . '" ' .
                   'data-field-number="' . $number . '">';
        }, $questionText);
        
        $html .= '<div class="passage-content">' . $questionText . '</div>';
        
        if ($isAdmin) {
            $html .= '<div class="admin-answers"><strong>Correct:</strong> ' . htmlspecialchars($question->correct_answer) . '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Simple Fill Blank
     */
    private static function renderSimpleFillBlank(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $html = '<div class="ielts-question fill-blank" data-question-id="' . $question->id . '">';
        
        // Question header
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">' . $questionNumber . '</span>';
        $html .= '</div>';
        
        $html .= '<div class="question-content">';
        $html .= '<div class="question-text">' . nl2br(htmlspecialchars($question->question_text)) . '</div>';
        
        $html .= '<div class="answer-container">';
        $html .= '<input type="text" ';
        $html .= 'name="answers[' . $question->id . ']" ';
        $html .= 'value="' . htmlspecialchars($userAnswer ?? '') . '" ';
        $html .= 'class="fill-blank-input" ';
        $html .= 'data-question-id="' . $question->id . '" ';
        $html .= 'placeholder="Your answer here...">';
        $html .= '</div>';
        $html .= '</div>';
        
        if ($isAdmin) {
            $html .= '<div class="admin-answers"><strong>Correct:</strong> ' . htmlspecialchars($question->correct_answer) . '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * True/False Questions
     */
    private static function renderTrueFalse(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $html = '<div class="ielts-question true-false" data-question-id="' . $question->id . '">';
        
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">' . $questionNumber . '</span>';
        $html .= '</div>';
        
        $html .= '<div class="question-content">';
        $html .= '<div class="question-text">' . nl2br(htmlspecialchars($question->question_text)) . '</div>';
        
        $html .= '<div class="true-false-options">';
        $html .= '<label class="radio-option">';
        $html .= '<input type="radio" name="answers[' . $question->id . ']" value="true" ' . ($userAnswer === 'true' ? 'checked' : '') . '>';
        $html .= '<span>True</span>';
        $html .= '</label>';
        $html .= '<label class="radio-option">';
        $html .= '<input type="radio" name="answers[' . $question->id . ']" value="false" ' . ($userAnswer === 'false' ? 'checked' : '') . '>';
        $html .= '<span>False</span>';
        $html .= '</label>';
        $html .= '</div>';
        $html .= '</div>';
        
        if ($isAdmin) {
            $html .= '<div class="admin-answers"><strong>Correct:</strong> ' . htmlspecialchars($question->correct_answer) . '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Drag and Drop Questions
     */
    private static function renderDragDrop(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $dragItems = is_array($question->drag_items) ? $question->drag_items : json_decode($question->drag_items, true) ?? [];
        $dropZones = is_array($question->drop_zones) ? $question->drop_zones : json_decode($question->drop_zones, true) ?? [];
        
        $html = '<div class="ielts-question drag-drop" data-question-id="' . $question->id . '">';
        
        // Question header
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">Questions ' . $questionNumber . '</span>';
        $html .= '</div>';
        
        // Instructions
        $html .= '<div class="question-instructions">';
        $html .= '<p>Choose <strong>FIVE</strong> answers from the box and write the correct letter, <strong>A-H</strong>, next to questions ' . $questionNumber . '.</p>';
        $html .= '</div>';
        
        // Question text
        $html .= '<div class="question-text">' . nl2br(htmlspecialchars($question->question_text)) . '</div>';
        
        // Matching table
        $html .= '<table class="matching-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Continent</th>';
        $html .= '<th>Answer</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        // Drop zones (table rows)
        foreach ($dropZones as $zone) {
            $zoneId = $zone['id'] ?? '';
            $zoneLabel = $zone['label'] ?? '';
            $zoneAnswer = is_array($userAnswer) ? ($userAnswer[$zoneId] ?? '') : '';
            
            $html .= '<tr>';
            $html .= '<td><strong>' . htmlspecialchars($zoneId) . '</strong> ' . htmlspecialchars($zoneLabel) . '</td>';
            $html .= '<td>';
            $html .= '<div class="drop-zone" data-zone-id="' . htmlspecialchars($zoneId) . '" data-question-id="' . $question->id . '">';
            
            if ($zoneAnswer) {
                $html .= '<span class="dropped-item">' . htmlspecialchars($zoneAnswer) . '</span>';
            } else {
                $html .= '<span class="drop-placeholder">Drop here</span>';
            }
            
            $html .= '<input type="hidden" name="answers[' . $question->id . '][' . $zoneId . ']" value="' . htmlspecialchars($zoneAnswer) . '" class="drop-input">';
            $html .= '</div>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        
        // Draggable options
        $html .= '<div class="drag-options">';
        $html .= '<div class="options-grid">';
        
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        foreach ($dragItems as $index => $item) {
            $letter = $letters[$index] ?? ($index + 1);
            $html .= '<div class="drag-item" data-value="' . htmlspecialchars($letter) . '" draggable="true">';
            $html .= '<span class="item-letter">' . $letter . '</span> ' . htmlspecialchars($item);
            $html .= '</div>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        if ($isAdmin) {
            $html .= '<div class="admin-answers"><strong>Correct:</strong> ' . htmlspecialchars($question->correct_answer) . '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Essay Questions
     */
    private static function renderEssay(Question $question, int $questionNumber, $userAnswer, bool $isAdmin = false)
    {
        $html = '<div class="ielts-question essay" data-question-id="' . $question->id . '">';
        
        $html .= '<div class="question-header">';
        $html .= '<span class="question-number">' . $questionNumber . '</span>';
        $html .= '<div class="word-limit-hint">' . ($question->word_limit ?? 'Minimum 250 words') . '</div>';
        $html .= '</div>';
        
        $html .= '<div class="question-content">';
        $html .= '<div class="question-text">' . nl2br(htmlspecialchars($question->question_text)) . '</div>';
        
        $html .= '<div class="essay-container">';
        $html .= '<textarea name="answers[' . $question->id . ']" ';
        $html .= 'class="essay-textarea" ';
        $html .= 'rows="15" ';
        $html .= 'data-question-id="' . $question->id . '" ';
        $html .= 'placeholder="Write your essay here...">';
        $html .= htmlspecialchars($userAnswer ?? '');
        $html .= '</textarea>';
        $html .= '<div class="word-counter">Words: <span class="word-count">0</span></div>';
        $html .= '</div>';
        $html .= '</div>';
        
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
}
