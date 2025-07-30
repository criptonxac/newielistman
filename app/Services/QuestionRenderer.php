<?php

namespace App\Services;

use App\Models\TestQuestion;

class QuestionRenderer
{
    /**
     * Render a question based on its type
     * 
     * @param TestQuestion $question
     * @param int $questionNumber
     * @param array $userAnswers
     * @return string
     */
    public static function render(TestQuestion $question, int $questionNumber, $userAnswers = [])
    {
        // Get user answer for this question if it exists
        $userAnswer = $userAnswers[$question->id] ?? null;
        
        // Render based on question type
        switch ($question->question_type) {
            case 'multiple_choice':
                return self::renderMultipleChoice($question, $questionNumber, $userAnswer);
            case 'true_false':
                return self::renderTrueFalse($question, $questionNumber, $userAnswer);
            case 'matching':
                return self::renderMatching($question, $questionNumber, $userAnswer);
            case 'drag_drop':
                return self::renderEnhancedDragDrop($question, $questionNumber, $userAnswer);
            case 'essay':
                return self::renderEssay($question, $questionNumber, $userAnswer);
            case 'fill_blanks':
                return self::renderFillBlanks($question, $questionNumber, $userAnswer);
            default:
                return self::renderDefault($question, $questionNumber, $userAnswer);
        }
    }
    
    /**
     * Enhanced Drag and Drop Question Renderer
     * Uses external CSS and JS files for clean separation
     */
    private static function renderEnhancedDragDrop(TestQuestion $question, int $questionNumber, $userAnswer)
    {
        // Parse options and targets from question data
        $options = is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []);
        $targets = is_array($question->targets) ? $question->targets : (json_decode($question->targets, true) ?? []);
        $correctAnswers = is_array($question->correct_answer) ? $question->correct_answer : (json_decode($question->correct_answer, true) ?? []);
        
        // Parse user answers if they exist
        $userAnswers = is_array($userAnswer) ? $userAnswer : (is_string($userAnswer) ? json_decode($userAnswer, true) : []);
        
        $questionId = $question->id;
        
        // Build HTML structure
        $html = '<div class="enhanced-drag-drop-container" data-question-id="' . $questionId . '">';
        
        // Question header
        $html .= '<div class="enhanced-question-header">';
        $html .= '<span class="enhanced-question-number">' . $questionNumber . '.</span>';
        $html .= '<span class="enhanced-question-text">' . htmlspecialchars($question->question_text) . '</span>';
        $html .= '</div>';
        
        // Main drag area
        $html .= '<div class="enhanced-drag-area">';
        
        // Left side - Available options to drag
        $html .= '<div class="enhanced-answers-bank">';
        $html .= '<h3 class="enhanced-section-title">📚 Available Options</h3>';
        $html .= '<div class="enhanced-draggable-items">';
        
        // Generate draggable items
        foreach ($targets as $index => $target) {
            $isUsed = in_array($target, array_values($userAnswers)) ? 'used' : '';
            $html .= '<div class="enhanced-draggable ' . $isUsed . '" ';
            $html .= 'draggable="true" ';
            $html .= 'data-value="' . htmlspecialchars($target) . '" ';
            $html .= 'data-question-id="' . $questionId . '">';
            $html .= htmlspecialchars($target);
            $html .= '</div>';
        }
        
        $html .= '</div>'; // End draggable-items
        $html .= '</div>'; // End answers-bank
        
        // Right side - Drop zones with labels
        $html .= '<div class="enhanced-drop-zones">';
        $html .= '<h3 class="enhanced-section-title">🎯 Drop Zones</h3>';
        $html .= '<div class="enhanced-drop-zone-list">';
        
        // Generate drop zones with labels
        foreach ($options as $index => $option) {
            $currentAnswer = $userAnswers[$index] ?? '';
            
            $html .= '<div class="enhanced-drop-zone-item">';
            
            // Label for this drop zone
            $html .= '<div class="enhanced-drop-zone-label">' . htmlspecialchars($option) . '</div>';
            
            // Drop zone
            $html .= '<div class="enhanced-drop-zone ' . ($currentAnswer ? 'filled' : '') . '" ';
            $html .= 'data-placeholder="Drop here" ';
            $html .= 'data-question-id="' . $questionId . '" ';
            $html .= 'data-index="' . $index . '" ';
            
            // Add correct answer data for validation
            if (isset($correctAnswers[$index])) {
                $html .= 'data-correct="' . htmlspecialchars($correctAnswers[$index]) . '" ';
            }
            
            $html .= '>';
            
            // If there's already an answer, show it
            if ($currentAnswer) {
                $html .= '<div class="enhanced-dropped-answer">';
                $html .= htmlspecialchars($currentAnswer);
                $html .= '<button class="enhanced-remove-btn" onclick="removeEnhancedAnswer(this)">&times;</button>';
                $html .= '</div>';
            }
            
            // Hidden input for form submission
            $html .= '<input type="hidden" name="answers[' . $questionId . '][' . $index . ']" value="' . htmlspecialchars($currentAnswer) . '">';
            
            $html .= '</div>'; // End drop-zone
            $html .= '</div>'; // End drop-zone-item
        }
        
        $html .= '</div>'; // End drop-zone-list
        $html .= '</div>'; // End drop-zones
        $html .= '</div>'; // End drag-area
        
        // Progress indicator (optional)
        $completedCount = count(array_filter($userAnswers));
        $totalCount = count($options);
        $progressPercent = $totalCount > 0 ? ($completedCount / $totalCount) * 100 : 0;
        
        $html .= '<div class="enhanced-progress-container">';
        $html .= '<div class="enhanced-progress-text">' . $completedCount . '/' . $totalCount . ' completed</div>';
        $html .= '<div class="enhanced-progress-bar">';
        $html .= '<div class="enhanced-progress-fill" style="width: ' . $progressPercent . '%"></div>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '</div>'; // End enhanced-drag-drop-container
        
        return $html;
    }
    
    /**
     * Render a multiple choice question (radio buttons)
     */
    private static function renderMultipleChoice(TestQuestion $question, int $questionNumber, $userAnswer)
    {
        $options = is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []);
        $html = '<div class="p-6 bg-gray-50 rounded-lg shadow-sm">';
        $html .= '<div class="mb-4">';
        $html .= '<span class="font-bold mr-2 text-lg">' . $questionNumber . '.</span>';
        $html .= $question->question_text;
        $html .= '</div>';
        
        $html .= '<div class="space-y-2">';
        foreach ($options as $index => $option) {
            $checked = ($userAnswer == $option) ? 'checked' : '';
            $html .= '<label class="flex items-center space-x-2">';
            $html .= '<input type="radio" name="answers[' . $question->id . ']" value="' . htmlspecialchars($option) . '" class="form-radio" ' . $checked . '>';
            $html .= '<span>' . htmlspecialchars($option) . '</span>';
            $html .= '</label>';
        }
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render a true/false question
     */
    private static function renderTrueFalse(TestQuestion $question, int $questionNumber, $userAnswer)
    {
        $html = '<div class="p-6 bg-gray-50 rounded-lg shadow-sm">';
        $html .= '<div class="mb-4">';
        $html .= '<span class="font-bold mr-2 text-lg">' . $questionNumber . '.</span>';
        $html .= $question->question_text;
        $html .= '</div>';
        
        $html .= '<div class="space-y-2">';
        $trueChecked = ($userAnswer == 'TRUE') ? 'checked' : '';
        $falseChecked = ($userAnswer == 'FALSE') ? 'checked' : '';
        $notGivenChecked = ($userAnswer == 'NOT GIVEN') ? 'checked' : '';
        
        $html .= '<label class="flex items-center space-x-2">';
        $html .= '<input type="radio" name="answers[' . $question->id . ']" value="TRUE" class="form-radio" ' . $trueChecked . '>';
        $html .= '<span>TRUE</span>';
        $html .= '</label>';
        
        $html .= '<label class="flex items-center space-x-2">';
        $html .= '<input type="radio" name="answers[' . $question->id . ']" value="FALSE" class="form-radio" ' . $falseChecked . '>';
        $html .= '<span>FALSE</span>';
        $html .= '</label>';
        
        $html .= '<label class="flex items-center space-x-2">';
        $html .= '<input type="radio" name="answers[' . $question->id . ']" value="NOT GIVEN" class="form-radio" ' . $notGivenChecked . '>';
        $html .= '<span>NOT GIVEN</span>';
        $html .= '</label>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render a matching question
     */
    private static function renderMatching(TestQuestion $question, int $questionNumber, $userAnswer)
    {
        $options = is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []);
        
        $html = '<div class="p-6 bg-gray-50 rounded-lg shadow-sm">';
        $html .= '<div class="mb-4">';
        $html .= '<span class="font-bold mr-2 text-lg">' . $questionNumber . '.</span>';
        $html .= $question->question_text;
        $html .= '</div>';
        
        $html .= '<div class="mt-2">';
        $html .= '<select name="answers[' . $question->id . ']" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">';
        $html .= '<option value="">Javobni tanlang</option>';
        
        foreach ($options as $option) {
            $selected = ($userAnswer == $option) ? 'selected' : '';
            $html .= '<option value="' . htmlspecialchars($option) . '" ' . $selected . '>' . htmlspecialchars($option) . '</option>';
        }
        
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render an essay question
     */
    private static function renderEssay(TestQuestion $question, int $questionNumber, $userAnswer)
    {
        $html = '<div class="p-6 bg-gray-50 rounded-lg shadow-sm">';
        $html .= '<div class="mb-4">';
        $html .= '<span class="font-bold mr-2 text-lg">' . $questionNumber . '.</span>';
        $html .= $question->question_text;
        $html .= '</div>';
        
        $html .= '<div class="mt-2">';
        $html .= '<textarea name="answers[' . $question->id . ']" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">' . htmlspecialchars($userAnswer ?? '') . '</textarea>';
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render a default question (usually fill-in-the-blank)
     */
    private static function renderDefault(TestQuestion $question, int $questionNumber, $userAnswer)
    {
        $html = '<div class="p-6 bg-gray-50 rounded-lg shadow-sm question">';
        $html .= '<div class="mb-4">';
        $html .= '<span class="font-bold mr-2 text-lg">' . $questionNumber . '.</span>';
        
        // Check if the question text contains placeholders for answers
        $pattern = '/_{3,}|\[\s*_+\s*\]|\[\s*blank\s*\]/i';
        
        if (preg_match($pattern, $question->question_text)) {
            // Replace placeholders with input fields
            $html .= preg_replace_callback($pattern, function($matches) use ($question, $userAnswer) {
                return '<input type="text" name="answers[' . $question->id . ']" value="' . htmlspecialchars($userAnswer ?? '') . '" class="inline-input shadow border-b-2 border-gray-300 px-1 mx-1 focus:outline-none focus:border-blue-500" style="width:150px; min-width:80px;">';
            }, $question->question_text);
        } else {
            $html .= $question->question_text;
            $html .= '<div class="mt-2">';
            $html .= '<input type="text" name="answers[' . $question->id . ']" value="' . htmlspecialchars($userAnswer ?? '') . '" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render a fill-in-the-blanks question
     */
    private static function renderFillBlanks(TestQuestion $question, int $questionNumber, $userAnswer)
    {
        $html = '<div class="p-6 bg-gray-50 rounded-lg shadow-sm question">';
        $html .= '<div class="mb-4">';
        $html .= '<span class="font-bold mr-2 text-lg">' . $questionNumber . '.</span>';
        
        $questionText = $question->question_text;
        $correctAnswers = is_array($question->correct_answer) ? $question->correct_answer : json_decode($question->correct_answer, true);
        
        if (!is_array($correctAnswers)) {
            $correctAnswers = [$correctAnswers];
        }
        
        $userAnswers = is_array($userAnswer) ? $userAnswer : (is_string($userAnswer) ? json_decode($userAnswer, true) : []);
        
        if (!is_array($userAnswers)) {
            $userAnswers = [$userAnswers];
        }
        
        $pattern = '/\[blank\]|_{3,}|\[\s*_+\s*\]/i';
        $blankCount = 0;
        
        $html .= preg_replace_callback($pattern, function($matches) use ($question, &$blankCount, $userAnswers) {
            $value = isset($userAnswers[$blankCount]) ? htmlspecialchars($userAnswers[$blankCount]) : '';
            $blankCount++;
            
            return '<input type="text" 
                        name="answers[' . $question->id . '][' . ($blankCount-1) . ']" 
                        value="' . $value . '" 
                        class="inline-input shadow border-b-2 border-gray-300 px-1 mx-1 focus:outline-none focus:border-blue-500" 
                        style="width:150px; min-width:80px;">';
        }, $questionText);
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
}