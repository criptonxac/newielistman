<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IELTS Listening Test</title>
    <link rel="stylesheet" href="{{ asset('css/listening.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <h1>IELTS Listening Test</h1>
                <div>Part 1: Questions 1-10</div>
            </div>
            <div class="timer" id="timer">30:00</div>
        </div>

        <!-- Navigation Section -->
        <div class="nav-section">
            Listening &gt; Part 1 &gt; Questions 1-10
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Question Panel -->
            <div class="question-panel">
                <!-- Part 1 Content -->
                <div class="part-content" data-part="1">
                    <div class="question-instruction">
                        <h2 style="color: #2c3e50; margin-bottom: 1rem;">Part 1</h2>
                        <strong>Listen and complete the notes below. Write NO MORE THAN TWO WORDS AND/OR A NUMBER for each answer.</strong>
                    </div>

                    <div class="form-section">
                        <h3 style="color: #2c3e50; margin-bottom: 1rem;">HOLIDAY RENTAL ENQUIRY</h3>
                        
                        <div class="form-questions">
                            <div class="form-question">
                                <label>1. Type of accommodation required:</label>
                                <input type="text" class="answer-input" data-question="1" placeholder="Answer">
                            </div>
                            
                            <div class="form-question">
                                <label>2. Number of people:</label>
                                <input type="text" class="answer-input" data-question="2" placeholder="Answer">
                            </div>
                            
                            <div class="form-question">
                                <label>3. Preferred location:</label>
                                <input type="text" class="answer-input" data-question="3" placeholder="Answer">
                            </div>
                            
                            <div class="form-question">
                                <label>4. Maximum budget per week: £</label>
                                <input type="text" class="answer-input" data-question="4" placeholder="Answer">
                            </div>
                            
                            <div class="form-question">
                                <label>5. Arrival date:</label>
                                <input type="text" class="answer-input" data-question="5" placeholder="Answer">
                            </div>
                            
                            <div class="form-question">
                                <label>6. Length of stay:</label>
                                <input type="text" class="answer-input" data-question="6" placeholder="Answer">
                            </div>
                            
                            <div class="form-question">
                                <label>7. Contact telephone number:</label>
                                <input type="text" class="answer-input" data-question="7" placeholder="Answer">
                            </div>
                            
                            <div class="form-question">
                                <label>8. Email address:</label>
                                <input type="text" class="answer-input" data-question="8" placeholder="Answer">
                            </div>
                            
                            <div class="form-question">
                                <label>9. Special requirements:</label>
                                <input type="text" class="answer-input" data-question="9" placeholder="Answer">
                            </div>
                            
                            <div class="form-question">
                                <label>10. Preferred method of payment:</label>
                                <input type="text" class="answer-input" data-question="10" placeholder="Answer">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Part 2 Content -->
                <div class="part-content" data-part="2" style="display: none;">
                    <div class="question-instruction">
                        <h2 style="color: #2c3e50; margin-bottom: 1rem;">Part 2</h2>
                        <strong>Listen and answer questions 11–20.</strong>
                    </div>

                    <!-- Questions 11-15: Staff Mapping -->
                    <div class="question-instruction" style="margin-top: 1.5rem;">
                        <strong>Questions 11–15</strong><br>
                        Who is responsible for each area? Choose the correct answer for each person.
                    </div>

                    <div style="display: flex; gap: 2rem; margin: 2rem 0;">
                        <div style="flex: 1;">
                            <h3 style="margin-bottom: 1rem;">People</h3>
                            <div class="staff-questions">
                                <div class="staff-question">
                                    <span>11. Mary Brown:</span>
                                    <select class="answer-select" data-question="11">
                                        <option value="">Choose answer</option>
                                        <option value="reception">Reception</option>
                                        <option value="restaurant">Restaurant</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="housekeeping">Housekeeping</option>
                                    </select>
                                </div>
                                <div class="staff-question">
                                    <span>12. David Wilson:</span>
                                    <select class="answer-select" data-question="12">
                                        <option value="">Choose answer</option>
                                        <option value="reception">Reception</option>
                                        <option value="restaurant">Restaurant</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="housekeeping">Housekeeping</option>
                                    </select>
                                </div>
                                <div class="staff-question">
                                    <span>13. Sarah Johnson:</span>
                                    <select class="answer-select" data-question="13">
                                        <option value="">Choose answer</option>
                                        <option value="reception">Reception</option>
                                        <option value="restaurant">Restaurant</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="housekeeping">Housekeeping</option>
                                    </select>
                                </div>
                                <div class="staff-question">
                                    <span>14. Michael Davis:</span>
                                    <select class="answer-select" data-question="14">
                                        <option value="">Choose answer</option>
                                        <option value="reception">Reception</option>
                                        <option value="restaurant">Restaurant</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="housekeeping">Housekeeping</option>
                                    </select>
                                </div>
                                <div class="staff-question">
                                    <span>15. Emma Thompson:</span>
                                    <select class="answer-select" data-question="15">
                                        <option value="">Choose answer</option>
                                        <option value="reception">Reception</option>
                                        <option value="restaurant">Restaurant</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="housekeeping">Housekeeping</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Questions 16-20: Multiple Choice -->
                    <div class="question-instruction" style="margin-top: 2rem;">
                        <strong>Questions 16–20</strong><br>
                        Choose the correct answer.
                    </div>

                    <div class="multiple-choice-questions">
                        <div class="question-block">
                            <strong>16. The hotel was built in:</strong>
                            <div class="options">
                                <label><input type="radio" name="q16" value="1920"> 1920</label>
                                <label><input type="radio" name="q16" value="1925"> 1925</label>
                                <label><input type="radio" name="q16" value="1930"> 1930</label>
                            </div>
                        </div>
                        
                        <div class="question-block">
                            <strong>17. The hotel has:</strong>
                            <div class="options">
                                <label><input type="radio" name="q17" value="50 rooms"> 50 rooms</label>
                                <label><input type="radio" name="q17" value="75 rooms"> 75 rooms</label>
                                <label><input type="radio" name="q17" value="100 rooms"> 100 rooms</label>
                            </div>
                        </div>
                        
                        <div class="question-block">
                            <strong>18. Breakfast is served until:</strong>
                            <div class="options">
                                <label><input type="radio" name="q18" value="9:00 AM"> 9:00 AM</label>
                                <label><input type="radio" name="q18" value="10:00 AM"> 10:00 AM</label>
                                <label><input type="radio" name="q18" value="11:00 AM"> 11:00 AM</label>
                            </div>
                        </div>
                        
                        <div class="question-block">
                            <strong>19. The swimming pool is:</strong>
                            <div class="options">
                                <label><input type="radio" name="q19" value="indoor"> Indoor</label>
                                <label><input type="radio" name="q19" value="outdoor"> Outdoor</label>
                                <label><input type="radio" name="q19" value="both"> Both indoor and outdoor</label>
                            </div>
                        </div>
                        
                        <div class="question-block">
                            <strong>20. Check-out time is:</strong>
                            <div class="options">
                                <label><input type="radio" name="q20" value="11:00 AM"> 11:00 AM</label>
                                <label><input type="radio" name="q20" value="12:00 PM"> 12:00 PM</label>
                                <label><input type="radio" name="q20" value="1:00 PM"> 1:00 PM</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Part 3 Content -->
                <div class="part-content" data-part="3" style="display: none;">
                    <div class="question-instruction">
                        <h2 style="color: #2c3e50; margin-bottom: 1rem;">Part 3</h2>
                        <strong>Listen and answer questions 21–30.</strong>
                    </div>

                    <!-- Questions 21-26: Food Categories -->
                    <div class="question-instruction" style="margin-top: 1.5rem;">
                        <strong>Questions 21–26</strong><br>
                        Which characteristics apply to each food category? Choose the correct answers.
                    </div>

                    <div class="food-categories">
                        <div class="category-section">
                            <h4>Category A: Traditional Foods</h4>
                            <div class="category-questions">
                                <div class="category-question">
                                    <span>21. This food type is considered a:</span>
                                    <select class="answer-select" data-question="21">
                                        <option value="">Choose answer</option>
                                        <option value="staple">Staple food</option>
                                        <option value="luxury">Luxury item</option>
                                        <option value="seasonal">Seasonal food</option>
                                    </select>
                                </div>
                                <div class="category-question">
                                    <span>22. Main preparation method:</span>
                                    <select class="answer-select" data-question="22">
                                        <option value="">Choose answer</option>
                                        <option value="boiling">Boiling</option>
                                        <option value="grilling">Grilling</option>
                                        <option value="steaming">Steaming</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="category-section">
                            <h4>Category B: Modern Foods</h4>
                            <div class="category-questions">
                                <div class="category-question">
                                    <span>23. Primary characteristic:</span>
                                    <select class="answer-select" data-question="23">
                                        <option value="">Choose answer</option>
                                        <option value="convenient">Convenient</option>
                                        <option value="nutritious">Nutritious</option>
                                        <option value="expensive">Expensive</option>
                                    </select>
                                </div>
                                <div class="category-question">
                                    <span>24. Storage requirement:</span>
                                    <select class="answer-select" data-question="24">
                                        <option value="">Choose answer</option>
                                        <option value="refrigerated">Refrigerated</option>
                                        <option value="frozen">Frozen</option>
                                        <option value="room temperature">Room temperature</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="category-section">
                            <h4>Category C: Organic Foods</h4>
                            <div class="category-questions">
                                <div class="category-question">
                                    <span>25. Main benefit:</span>
                                    <select class="answer-select" data-question="25">
                                        <option value="">Choose answer</option>
                                        <option value="health">Health benefits</option>
                                        <option value="taste">Better taste</option>
                                        <option value="environment">Environmental impact</option>
                                    </select>
                                </div>
                                <div class="category-question">
                                    <span>26. Price comparison:</span>
                                    <select class="answer-select" data-question="26">
                                        <option value="">Choose answer</option>
                                        <option value="higher">Higher than conventional</option>
                                        <option value="same">Same as conventional</option>
                                        <option value="lower">Lower than conventional</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Questions 27-30: Gap Fill -->
                    <div class="question-instruction" style="margin-top: 2rem;">
                        <strong>Questions 27–30</strong><br>
                        Complete the sentences below. Write NO MORE THAN TWO WORDS for each answer.
                    </div>

                    <div class="gap-fill-questions">
                        <div class="gap-question">
                            <span>27. The research was conducted over a period of </span>
                            <input type="text" class="answer-input" data-question="27" placeholder="Answer">
                            <span> months.</span>
                        </div>
                        <div class="gap-question">
                            <span>28. Participants were asked to keep a </span>
                            <input type="text" class="answer-input" data-question="28" placeholder="Answer">
                            <span> of their eating habits.</span>
                        </div>
                        <div class="gap-question">
                            <span>29. The most significant finding was related to </span>
                            <input type="text" class="answer-input" data-question="29" placeholder="Answer">
                            <span> consumption.</span>
                        </div>
                        <div class="gap-question">
                            <span>30. Future research will focus on </span>
                            <input type="text" class="answer-input" data-question="30" placeholder="Answer">
                            <span> dietary patterns.</span>
                        </div>
                    </div>
                </div>

                <!-- Part 4 Content -->
                <div class="part-content" data-part="4" style="display: none;">
                    <div class="question-instruction">
                        <h2 style="color: #2c3e50; margin-bottom: 1rem;">Part 4</h2>
                        <strong>Listen and answer questions 31–40.</strong>
                    </div>

                    <!-- Questions 31-32: Multiple Choice -->
                    <div class="question-instruction" style="margin-top: 1.5rem;">
                        <strong>Questions 31–32</strong><br>
                        Choose the correct answer.
                    </div>

                    <div class="multiple-choice-questions">
                        <div class="question-block">
                            <strong>31. Participants in the Liberal Renaissance Study came from the same:</strong>
                            <div class="options">
                                <label><input type="radio" name="q31" value="age group"> age group</label>
                                <label><input type="radio" name="q31" value="geographical area"> geographical area</label>
                                <label><input type="radio" name="q31" value="socio-economic level"> socio-economic level</label>
                            </div>
                        </div>
                        
                        <div class="question-block">
                            <strong>32. The main focus of the study was:</strong>
                            <div class="options">
                                <label><input type="radio" name="q32" value="education"> Educational achievement</label>
                                <label><input type="radio" name="q32" value="health"> Health outcomes</label>
                                <label><input type="radio" name="q32" value="lifestyle"> Lifestyle changes</label>
                            </div>
                        </div>
                    </div>

                    <!-- Questions 33-40: Sentence Completion -->
                    <div class="question-instruction" style="margin-top: 2rem;">
                        <strong>Questions 33–40</strong><br>
                        Complete the sentences below. Write NO MORE THAN THREE WORDS for each answer.
                    </div>

                    <div class="sentence-completion">
                        <div class="completion-question">
                            <span>33. The study began in </span>
                            <input type="text" class="answer-input" data-question="33" placeholder="Answer">
                            <span> and lasted for five years.</span>
                        </div>
                        
                        <div class="completion-question">
                            <span>34. Researchers collected data through </span>
                            <input type="text" class="answer-input" data-question="34" placeholder="Answer">
                            <span> and personal interviews.</span>
                        </div>
                        
                        <div class="completion-question">
                            <span>35. The most surprising result was the impact on </span>
                            <input type="text" class="answer-input" data-question="35" placeholder="Answer">
                            <span> levels among participants.</span>
                        </div>
                        
                        <div class="completion-question">
                            <span>36. Participants showed improvement in their </span>
                            <input type="text" class="answer-input" data-question="36" placeholder="Answer">
                            <span> skills over time.</span>
                        </div>
                        
                        <div class="completion-question">
                            <span>37. The control group received </span>
                            <input type="text" class="answer-input" data-question="37" placeholder="Answer">
                            <span> instead of the main treatment.</span>
                        </div>
                        
                        <div class="completion-question">
                            <span>38. Data analysis revealed significant differences in </span>
                            <input type="text" class="answer-input" data-question="38" placeholder="Answer">
                            <span> between the two groups.</span>
                        </div>
                        
                        <div class="completion-question">
                            <span>39. The findings have implications for </span>
                            <input type="text" class="answer-input" data-question="39" placeholder="Answer">
                            <span> policy development.</span>
                        </div>
                        
                        <div class="completion-question">
                            <span>40. Future research will investigate the </span>
                            <input type="text" class="answer-input" data-question="40" placeholder="Answer">
                            <span> effects of the intervention.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audio Panel -->
            <div class="audio-panel">
                <div class="audio-player">
                    <h3>Audio Player</h3>
                    <div class="audio-controls">
                        <button class="play-btn" id="playBtn">▶</button>
                        <div class="volume-control">
                            <span>🔊</span>
                            <input type="range" class="volume-slider" min="0" max="100" value="70">
                        </div>
                    </div>
                    <audio id="audioPlayer" controls style="width: 100%; margin-top: 1rem;">
                        <source src="listening-part1.mp3" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>

                <div style="background: white; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                    <h4>Instructions</h4>
                    <ul style="margin-top: 0.5rem; padding-left: 1.5rem; font-size: 0.9rem;">
                        <li>Listen to the audio carefully</li>
                        <li>Type your answers in the input fields</li>
                        <li>Press Enter to move to next field</li>
                        <li>Answers are auto-saved</li>
                        <li>ONE WORD AND/OR NUMBER per answer</li>
                    </ul>
                </div>

                <div style="background: #e8f4fd; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                    <h4>Progress</h4>
                    <div id="progressText">0/10 questions answered</div>
                    <div style="margin-top: 0.5rem;">
                        <div style="background: #ddd; height: 10px; border-radius: 5px;">
                            <div id="progressBar" style="background: #3498db; height: 100%; width: 0%; border-radius: 5px; transition: width 0.3s;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress and Navigation -->
        <div class="progress-section">
            <button class="nav-btn secondary" onclick="window.location.href='listening.html'">Main Test</button>
            <div class="progress-bar">
                <div class="progress-fill" style="width: 25%;"></div>
            </div>
            <button class="nav-btn primary" onclick="window.location.href='part2.html'">Next: Part 2</button>
        </div>
    </div>

    <script src="js/listening.js"></script>
    <script>
        // Update for Part 1 only
        totalQuestions = 10;
        
        // Override progress function for Part 1
        function updateProgress() {
            const progressText = document.getElementById('progressText');
            const progressBar = document.getElementById('progressBar');
            const percentage = (answeredCount / totalQuestions) * 100;
            
            progressText.textContent = `${answeredCount}/${totalQuestions} questions answered`;
            progressBar.style.width = percentage + '%';
        }


<script src="{{ asset('js/listening-test.js') }}"></script>
</body>
</html>
