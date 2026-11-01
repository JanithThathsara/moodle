/**
 * OES Theme - Exam Question Navigation & UX Enhancements.
 * Automatically adds a "Next" button under every question in the exam paper.
 *
 * @package    theme_oes
 * @copyright  2026 Mahapola Ports & Maritime Academy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
(function() {
    'use strict';

    function initExamQuestionNavigation() {
        // Find all question containers on the quiz attempt or review page
        var questions = document.querySelectorAll('.que');
        if (!questions || questions.length === 0) {
            return;
        }

        var totalQuestions = questions.length;
        var nativeNextBtn = document.getElementById('mod_quiz-next-nav');
        var quizForm = document.getElementById('responseform');

        // Check if sequential navigation is enforced by quiz settings
        var isSequential = false;
        var navBlock = document.getElementById('mod_quiz_navblock');
        if (navBlock && navBlock.querySelector('.qnbutton.sequential')) {
            isSequential = true;
        }

        questions.forEach(function(que, index) {
            // Guard against duplicate injection
            if (que.querySelector('.oes-question-action-bar')) {
                return;
            }

            var questionNum = index + 1;
            var isFirst = (index === 0);
            var isLast = (index === totalQuestions - 1);

            // Main action bar container under the question
            var actionBar = document.createElement('div');
            actionBar.className = 'oes-question-action-bar';

            // Left side: Status indicator badge
            var statusDiv = document.createElement('div');
            statusDiv.className = 'oes-question-status-wrap';

            var hasAnswer = checkIfAnswered(que);
            var statusBadge = document.createElement('span');
            statusBadge.className = 'oes-answer-badge ' + (hasAnswer ? 'oes-answered' : 'oes-unanswered');
            statusBadge.innerHTML = hasAnswer 
                ? '<i class="fa fa-check-circle me-1"></i> Answer Selected' 
                : '<i class="fa fa-circle-o me-1"></i> Not answered yet';

            statusDiv.appendChild(statusBadge);

            // Right side: Button group
            var btnGroup = document.createElement('div');
            btnGroup.className = 'oes-question-btn-group';

            // Previous Button (if not first question on page and not sequential)
            if (!isFirst && !isSequential) {
                var prevBtn = document.createElement('button');
                prevBtn.type = 'button';
                prevBtn.className = 'btn oes-btn-prev-question';
                prevBtn.innerHTML = '<i class="fa fa-arrow-left me-1"></i> Previous';
                prevBtn.title = 'Return to Question ' + index;
                prevBtn.setAttribute('aria-label', 'Previous question');
                prevBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var prevQue = questions[index - 1];
                    if (prevQue) {
                        scrollToQuestion(prevQue);
                    }
                });
                btnGroup.appendChild(prevBtn);
            }

            // Next Button (Always present under every single question!)
            var nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.className = 'btn btn-primary oes-btn-next-question';
            nextBtn.setAttribute('aria-label', 'Next question');

            if (!isLast) {
                // If not last question on current page, scroll to next question
                nextBtn.innerHTML = 'Next <i class="fa fa-arrow-right ms-2"></i>';
                nextBtn.title = 'Proceed to Question ' + (questionNum + 1);
                nextBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var nextQue = questions[index + 1];
                    if (nextQue) {
                        scrollToQuestion(nextQue);
                    }
                });
            } else {
                // Last question on this page -> proceeds to next page or finish attempt
                var nextLabel = 'Next <i class="fa fa-arrow-right ms-2"></i>';
                if (nativeNextBtn) {
                    var val = (nativeNextBtn.value || nativeNextBtn.innerText || '').toLowerCase();
                    if (val.indexOf('finish') !== -1 || val.indexOf('end') !== -1) {
                        nextLabel = 'Finish Attempt <i class="fa fa-check-circle ms-2"></i>';
                        nextBtn.classList.add('oes-btn-finish');
                    } else if (val.indexOf('next') !== -1) {
                        nextLabel = 'Next Page <i class="fa fa-arrow-right ms-2"></i>';
                    }
                }
                nextBtn.innerHTML = nextLabel;
                nextBtn.title = 'Submit page and advance';

                nextBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (nativeNextBtn) {
                        nextBtn.disabled = true;
                        nextBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Saving...';
                        nativeNextBtn.click();
                    } else if (quizForm) {
                        quizForm.submit();
                    }
                });
            }

            btnGroup.appendChild(nextBtn);

            actionBar.appendChild(statusDiv);
            actionBar.appendChild(btnGroup);

            // Append action bar inside question content
            var contentDiv = que.querySelector('.content');
            if (contentDiv) {
                contentDiv.appendChild(actionBar);
            } else {
                que.appendChild(actionBar);
            }

            // Dynamically update answer status on radio/checkbox/text/select change
            que.addEventListener('change', function() {
                var ans = checkIfAnswered(que);
                if (ans) {
                    statusBadge.className = 'oes-answer-badge oes-answered';
                    statusBadge.innerHTML = '<i class="fa fa-check-circle me-1"></i> Answer Selected';
                } else {
                    statusBadge.className = 'oes-answer-badge oes-unanswered';
                    statusBadge.innerHTML = '<i class="fa fa-circle-o me-1"></i> Not answered yet';
                }
            });

            // Also watch text input keystrokes
            que.addEventListener('input', function(e) {
                if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA')) {
                    var ans = checkIfAnswered(que);
                    if (ans) {
                        statusBadge.className = 'oes-answer-badge oes-answered';
                        statusBadge.innerHTML = '<i class="fa fa-check-circle me-1"></i> Answer Selected';
                    } else {
                        statusBadge.className = 'oes-answer-badge oes-unanswered';
                        statusBadge.innerHTML = '<i class="fa fa-circle-o me-1"></i> Not answered yet';
                    }
                }
            });
        });
    }

    /**
     * Checks if the question currently has an answered value.
     */
    function checkIfAnswered(que) {
        if (!que) return false;

        // Radio or checkbox selection
        var checkedInput = que.querySelector('input[type="radio"]:checked, input[type="checkbox"]:checked');
        if (checkedInput) {
            return true;
        }

        // Text input or essay response
        var textInputs = que.querySelectorAll('input[type="text"], textarea');
        for (var i = 0; i < textInputs.length; i++) {
            if (textInputs[i].value && textInputs[i].value.trim().length > 0) {
                return true;
            }
        }

        // Dropdown selection
        var selects = que.querySelectorAll('select');
        for (var j = 0; j < selects.length; j++) {
            if (selects[j].value && selects[j].value !== '' && selects[j].value !== '0') {
                return true;
            }
        }

        return false;
    }

    /**
     * Smoothly scrolls to target question accounting for sticky navigation bar.
     */
    function scrollToQuestion(targetQue) {
        if (!targetQue) return;

        var headerOffset = 95;
        var stickyNavbar = document.querySelector('.oes-navbar-secure, .navbar, .fixed-top');
        if (stickyNavbar) {
            headerOffset = stickyNavbar.offsetHeight + 20;
        }

        var securityBanner = document.querySelector('.oes-exam-security-banner');
        if (securityBanner) {
            headerOffset += securityBanner.offsetHeight;
        }

        var rect = targetQue.getBoundingClientRect();
        var targetPosition = window.pageYOffset + rect.top - headerOffset;

        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });

        // Add vibrant highlight animation to the targeted question
        targetQue.classList.add('oes-highlight-active');
        setTimeout(function() {
            targetQue.classList.remove('oes-highlight-active');
        }, 1600);

        // Accessibility focus
        var focusable = targetQue.querySelector('input:not([type="hidden"]), select, textarea, .qtext');
        if (focusable) {
            try {
                focusable.focus({ preventScroll: true });
            } catch (err) {}
        }
    }

    // Bootstrapping
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initExamQuestionNavigation);
    } else {
        initExamQuestionNavigation();
    }

    window.addEventListener('load', function() {
        setTimeout(initExamQuestionNavigation, 150);
    });

    // Support Moodle AJAX / dynamic question reloads
    if (typeof window.M !== 'undefined' && window.M.yui) {
        try {
            window.M.yui.use('event', function(Y) {
                Y.on('domready', initExamQuestionNavigation);
            });
        } catch (e) {}
    }
})();
