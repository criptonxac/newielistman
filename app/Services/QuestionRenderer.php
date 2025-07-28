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
    public static function render(TestQuestion $question, int $questionNumber, array $userAnswers = [])
    {
        $userAnswer = $userAnswers[$question->id] ?? null;
        
        switch ($question->question_type) {
            case 'multiple_choice':
                return self::renderMultipleChoice($question, $questionNumber, $userAnswer);
            case 'multiple_answer':
                return self::renderMultipleAnswer($question, $questionNumber, $userAnswer);
            case 'true_false':
                return self::renderTrueFalse($question, $questionNumber, $userAnswer);
            case 'fill_blank':
                return self::renderFillBlank($question, $questionNumber, $userAnswer);
            case 'matching':
                return self::renderMatching($question, $questionNumber, $userAnswer);
            case 'drag_drop':
                return self::renderDragDrop($question, $questionNumber, $userAnswer);
            case 'essay':
                return self::renderEssay($question, $questionNumber, $userAnswer);
            default:
                return self::renderDefault($question, $questionNumber, $userAnswer);
        }
    }
    
    /**
     * Render a multiple choice question (radio buttons)
     */
    private static function renderMultipleChoice(TestQuestion $question, int $questionNumber, $userAnswer)
    {
        $options = json_decode($question->options, true) ?? [];
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
        $options = json_decode($question->options, true) ?? [];
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
        $options = json_decode($question->options, true) ?? [];
        
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
        $options = json_decode($question->options, true) ?? [];
        $mappingTarget = $question->mapping_target ?? '';
        
        $html = '<div class="p-6 bg-gray-50 rounded-lg shadow-sm drag-drop-question" data-question-id="' . $question->id . '">';
        $html .= '<div class="mb-4">';
        $html .= '<span class="font-bold mr-2 text-lg">' . $questionNumber . '.</span>';
        $html .= $question->question_text;
        $html .= '</div>';
        
        // Draggable options
        $html .= '<div class="draggable-container mb-4 flex flex-wrap gap-2">';
        foreach ($options as $index => $option) {
            $html .= '<div class="draggable bg-blue-100 p-2 rounded cursor-move border border-blue-300" draggable="true" data-value="' . htmlspecialchars($option) . '">';
            $html .= htmlspecialchars($option);
            $html .= '</div>';
        }
        $html .= '</div>';
        
        // Drop zone
        $html .= '<div class="drop-zone-container">';
        $html .= '<div class="flex items-center mb-2">';
        $html .= '<span class="mr-2">' . htmlspecialchars($mappingTarget) . ':</span>';
        $html .= '<div class="drop-zone border-2 border-dashed border-gray-300 p-3 rounded-md flex-1 min-h-[40px] relative">';
        
        if ($userAnswer) {
            $html .= '<div class="draggable bg-blue-100 p-2 rounded cursor-not-allowed border border-blue-300" draggable="false">';
            $html .= htmlspecialchars($userAnswer);
            $html .= '</div>';
            $html .= '<button class="absolute top-1 right-1 text-gray-500 hover:text-red-500">&times;</button>';
        } else {
            $html .= '<div class="placeholder">Drop answer here</div>';
        }
        
        $html .= '<input type="hidden" name="answers[' . $question->id . ']" value="' . htmlspecialchars($userAnswer ?? '') . '">';
        $html .= '</div>';
        $html .= '</div>';
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
     * Render a default question
     */
    private static function renderDefault(TestQuestion $question, int $questionNumber, $userAnswer)
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
}
