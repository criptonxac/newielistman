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
                return self::renderDragDrop($question, $questionNumber, $userAnswer);
            case 'essay':
                return self::renderEssay($question, $questionNumber, $userAnswer);
            case 'fill_blanks':
                return self::renderFillBlanks($question, $questionNumber, $userAnswer);
            default:
                return self::renderDefault($question, $questionNumber, $userAnswer);
        }
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
     * Render a multiple answer question (checkboxes)
     */
    private static function renderMultipleAnswer(TestQuestion $question, int $questionNumber, $userAnswer)
    {
        $options = is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []);
        $userAnswers = is_array($userAnswer) ? $userAnswer : [];
        
        $html = '<div class="p-6 bg-gray-50 rounded-lg shadow-sm">';
        $html .= '<div class="mb-4">';
        $html .= '<span class="font-bold mr-2 text-lg">' . $questionNumber . '.</span>';
        $html .= $question->question_text;
        $html .= '</div>';
        
        $html .= '<div class="space-y-2">';
        foreach ($options as $index => $option) {
            $checked = in_array($option, $userAnswers) ? 'checked' : '';
            $html .= '<label class="flex items-center space-x-2">';
            $html .= '<input type="checkbox" name="answers[' . $question->id . '][]" value="' . htmlspecialchars($option) . '" class="form-checkbox" ' . $checked . '>';
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
     * Render a fill in the blank question
     */
    private static function renderFillBlank(TestQuestion $question, int $questionNumber, $userAnswer)
    {
        $html = '<div class="p-6 bg-gray-50 rounded-lg shadow-sm">';
        $html .= '<div class="mb-4">';
        $html .= '<span class="font-bold mr-2 text-lg">' . $questionNumber . '.</span>';
        $html .= $question->question_text;
        $html .= '</div>';
        
        $html .= '<div class="mt-2">';
        $html .= '<input type="text" name="answers[' . $question->id . ']" value="' . htmlspecialchars($userAnswer ?? '') . '" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">';
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
     * Render a drag and drop question
     */
    private static function renderDragDrop(TestQuestion $question, int $questionNumber, $userAnswer)
    {
        // Parse options and targets from question data
        $options = is_array($question->options) ? $question->options : (json_decode($question->options, true) ?? []);
        $targets = is_array($question->targets) ? $question->targets : (json_decode($question->targets, true) ?? []);
        $correctAnswers = is_array($question->correct_answer) ? $question->correct_answer : (json_decode($question->correct_answer, true) ?? []);
        
        // Parse user answers if they exist
        $userAnswers = is_array($userAnswer) ? $userAnswer : (is_string($userAnswer) ? json_decode($userAnswer, true) : []);
        
        // Main container
        $html = '<div class="p-6 bg-gray-50 rounded-lg shadow-sm drag-drop-question" data-question-id="' . $question->id . '">';
        $html .= '<div class="mb-4">';
        $html .= '<span class="font-bold mr-2 text-lg">' . $questionNumber . '.</span>';
        $html .= $question->question_text;
        $html .= '</div>';
        
        // Create a two-column layout
        $html .= '<div class="grid grid-cols-2 gap-6">';
        
        // Left column - items to match
        $html .= '<div class="left-column">';
        $html .= '<h3 class="font-bold text-center mb-4">Items</h3>';
        $html .= '<div class="space-y-4">';
        
        // Generate each item with its drop zone
        foreach ($options as $index => $option) {
            $itemId = $question->id . '_' . $index;
            $currentAnswer = $userAnswers[$index] ?? '';
            $answerClass = '';
            
            // Check if this answer has been submitted and evaluated
            if (isset($userAnswers[$index]) && isset($correctAnswers[$index])) {
                if ($userAnswers[$index] == $correctAnswers[$index]) {
                    $answerClass = 'bg-green-100 border-green-400'; // Correct answer
                } else {
                    $answerClass = 'bg-red-100 border-red-400'; // Wrong answer
                }
            } else {
                $answerClass = 'bg-blue-100 border-blue-300'; // Not evaluated yet
            }
            
            $html .= '<div class="flex items-center">';
            $html .= '<div class="flex-grow">' . htmlspecialchars($option) . '</div>';
            
            // Drop zone for this item
            $html .= '<div class="drop-zone ml-4 border-2 border-dashed border-gray-300 p-2 rounded-md w-32 h-10 flex items-center justify-center relative" data-item-id="' . $itemId . '">';
            
            if ($currentAnswer) {
                $html .= '<div class="dragged-answer ' . $answerClass . ' p-2 rounded w-full h-full flex items-center justify-center">';
                $html .= htmlspecialchars($currentAnswer);
                $html .= '</div>';
                $html .= '<button class="remove-answer absolute -top-2 -right-2 bg-white rounded-full w-5 h-5 flex items-center justify-center text-gray-500 hover:text-red-500 border border-gray-300">&times;</button>';
            } else {
                $html .= '<div class="placeholder text-gray-400 text-sm">Drop here</div>';
            }
            
            $html .= '<input type="hidden" name="answers[' . $question->id . '][' . $index . ']" value="' . htmlspecialchars($currentAnswer) . '">';
            $html .= '</div>'; // End drop zone
            $html .= '</div>'; // End flex container
        }
        
        $html .= '</div>'; // End space-y-4
        $html .= '</div>'; // End left column
        
        // Right column - draggable answers
        $html .= '<div class="right-column">';
        $html .= '<h3 class="font-bold text-center mb-4">Answers</h3>';
        $html .= '<div class="draggable-container flex flex-wrap gap-2">';
        
        // Generate draggable answers
        foreach ($targets as $target) {
            $html .= '<div class="draggable bg-blue-100 p-2 rounded cursor-move border border-blue-300" draggable="true" data-value="' . htmlspecialchars($target) . '">';
            $html .= htmlspecialchars($target);
            $html .= '</div>';
        }
        
        $html .= '</div>'; // End draggable-container
        $html .= '</div>'; // End right column
        
        $html .= '</div>'; // End grid
        $html .= '</div>'; // End main container
        
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
     * Render a default question
     */
    private static function renderDefault(TestQuestion $question, int $questionNumber, $userAnswer)
    {
        $html = '<div class="p-6 bg-gray-50 rounded-lg shadow-sm question">';
        $html .= '<div class="mb-4">';
        $html .= '<span class="font-bold mr-2 text-lg">' . $questionNumber . '.</span>';
        
        // Check if the question text contains placeholders for answers (marked with underscores or blank spaces)
        $pattern = '/_{3,}|\[\s*_+\s*\]|\[\s*blank\s*\]/i';
        
        if (preg_match($pattern, $question->question_text)) {
            // Replace placeholders with input fields
            $html .= preg_replace_callback($pattern, function($matches) use ($question, $userAnswer) {
                return '<input type="text" name="answers[' . $question->id . ']" value="' . htmlspecialchars($userAnswer ?? '') . '" class="inline-input shadow border-b-2 border-gray-300 px-1 mx-1 focus:outline-none focus:border-blue-500" style="width:150px; min-width:80px;">';
            }, $question->question_text);
        } else {
            // If no placeholders found, show the question text and a separate input field
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
        
        // Get the question text and correct answers
        $questionText = $question->question_text;
        $correctAnswers = is_array($question->correct_answer) ? $question->correct_answer : json_decode($question->correct_answer, true);
        
        if (!is_array($correctAnswers)) {
            $correctAnswers = [$correctAnswers];
        }
        
        // Parse user answers if they exist
        $userAnswers = is_array($userAnswer) ? $userAnswer : (is_string($userAnswer) ? json_decode($userAnswer, true) : []);
        
        if (!is_array($userAnswers)) {
            $userAnswers = [$userAnswers];
        }
        
        // Replace blanks with input fields
        $pattern = '/\[blank\]|_{3,}|\[\s*_+\s*\]/i';
        $blankCount = 0;
        
        $html .= preg_replace_callback($pattern, function($matches) use ($question, &$blankCount, $userAnswers) {
            $blankId = $question->id . '_' . $blankCount;
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
