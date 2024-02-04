<!DOCTYPE html>
<html>
<head>
    <title>Exam Page</title>
</head>
<body>
<script type="text/javascript">
   // Function to prevent users from going back to the previous page
   function preventBack() {
      window.history.forward();
   }
   setTimeout("preventBack()", 0);
   window.onunload = function() {
      null;
   };

   // Function to start the countdown timer and submit the form when the timer reaches 0
   function startTimer() {
      var timeLimit = parseInt(document.getElementById('timeExamLimit').value); // Exam time limit in seconds
      var display = document.getElementById("txt");
      var submitFormBtn = document.getElementById("submitAnswerFrmBtn");
      var remainingTime = timeLimit;

      function updateTimer() {
         var minutes = Math.floor(remainingTime / 60);
         var seconds = remainingTime % 60;
         var formattedTime = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
         display.value = formattedTime;

         // Update the hidden input field with remaining time
         document.getElementById("remainingTime").value = remainingTime;

         if (remainingTime <= 0) {
            // Time's up, submit the form
            document.getElementById("examAction").value = "Submit"; // Add this line
            submitFormBtn.click();
         } else {
            remainingTime--;
            setTimeout(updateTimer, 1000);
         }
      }

      updateTimer();
   }
   
   // Start the timer when the page loads
   window.onload = startTimer;
</script>

<?php
$examId = $_GET['id'];
$selExam = $conn->query("SELECT * FROM exam_tbl WHERE ex_id='$examId'")->fetch(PDO::FETCH_ASSOC);
$selExamTimeLimit = $selExam['ex_time_limit'];
$exDisplayLimit = $selExam['ex_questlimit_display'];
?>

<!-- HTML content for the exam page -->
<div class="app-main__outer">
   <div class="app-main__inner">
      <div class="col-md-12">
         <!-- Display exam title and description -->
         <div class="app-page-title">
            <div class="page-title-wrapper">
               <div class="page-title-heading">
                  <div>
                     <?php echo $selExam['ex_title']; ?>
                     <div class="page-title-subheading">
                        <?php echo $selExam['ex_description']; ?>
                     </div>
                  </div>
               </div>
               <!-- Display remaining time for the exam -->
              <div class="page-title-actions mr-5" style="font-size: 20px;">
                  <form name="cd" style="z-index: 155;position: fixed;right: 7%;">
                     <input type="hidden" name="" id="timeExamLimit" value="<?php echo $selExamTimeLimit; ?>">
                     <label>Remaining Time : </label>
                     <input style="border:none;background-color: transparent;color:blue;font-size: 25px;" name="disp" type="text" class="clock" id="txt" value="00:00" size="5" readonly="true" />
                  </form>
               </div>
            </div>
         </div>
      </div>

      <div class="col-md-12 p-0 mb-4">
         <!-- Form to submit answers -->
         <form method="post" id="submitAnswerFrm">
            <input type="hidden" name="exam_id" id="exam_id" value="<?php echo $examId; ?>">
            <input type="hidden" name="examAction" id="examAction">
            <input type="hidden" name="remainingTime" id="remainingTime" value="">
            <table class="align-middle mb-0 table table-borderless table-striped table-hover" id="tableList">
               <?php
               // Retrieve and shuffle questions for the exam
               $selQuest = $conn->query("SELECT * FROM exam_question_tbl WHERE exam_id='$examId'")->fetchAll(PDO::FETCH_ASSOC);
               shuffle($selQuest);

               // Limit the number of questions to display (25 questions)
               $questionsToDisplay = array_slice($selQuest, 0, 40);

               if (count($questionsToDisplay) > 0) {
                  $i = 1;
                  foreach ($questionsToDisplay as $selQuestRow) {
                     $questId = $selQuestRow['eqt_id'];

                     // Check if the content is an image
                     $isImage = isImage($selQuestRow['exam_question']);

                     echo '<tr>';
                     echo '<td>';

                     if ($isImage) {
                        // Display the image
                        echo '<img src="data:image/jpeg;base64,' . base64_encode($selQuestRow['exam_question']) . '" alt="Image" />';
                     } else {
                        // Display as a text-based question
                        echo '<p><img src="data:image/jpeg;base64,' . base64_encode($selQuestRow['exam_question']) . '" alt="Image" /></p>';
                        
                        // Display choices for text-based question
                      // Shuffle the answer choices
$answerChoices = array(
    $selQuestRow['exam_ch1'],
    $selQuestRow['exam_ch2'],
    $selQuestRow['exam_ch3'],
    $selQuestRow['exam_ch4']
);
shuffle($answerChoices);

// Display shuffled answer choices without the required attribute
echo '<div class="col-md-4 float-left">';
echo '<div class="form-group pl-4">';
echo '<input name="answer[' . $questId . '][correct]" value="' . $answerChoices[0] . '" class="form-check-input" type="radio" value="" id="invalidCheck">';
echo '<label class="form-check-label" for="invalidCheck">' . $answerChoices[0] . '</label>';
echo '</div>';
echo '<div class="form-group pl-4">';
echo '<input name="answer[' . $questId . '][correct]" value="' . $answerChoices[1] . '" class="form-check-input" type="radio" value="" id="invalidCheck">';
echo '<label class="form-check-label" for="invalidCheck">' . $answerChoices[1] . '</label>';
echo '</div>';
echo '</div>';
echo '<div class="col-md-8 float-left">';
echo '<div class="form-group pl-4">';
echo '<input name="answer[' . $questId . '][correct]" value="' . $answerChoices[2] . '" class="form-check-input" type="radio" value="" id="invalidCheck">';
echo '<label class="form-check-label" for="invalidCheck">' . $answerChoices[2] . '</label>';
echo '</div>';
echo '<div class="form-group pl-4">';
echo '<input name="answer[' . $questId . '][correct]" value="' . $answerChoices[3] . '" class="form-check-input" type="radio" value="" id="invalidCheck">';
echo '<label class="form-check-label" for="invalidCheck">' . $answerChoices[3] . '</label>';
echo '</div>';
echo '</div>';

                     }

                     echo '</td>';
                     echo '</tr>';
                  }
                  ?>
                  <tr>
                     <td style="padding: 20px;">
                        <!-- Buttons -->
                        <button type="button" class="btn btn-xlg btn-warning p-3 pl-4 pr-4" id="resetExamFrm">Reset</button>
                        <input name="submit" type="submit" value="Submit" class="btn btn-xlg btn-primary p-3 pl-4 pr-4 float-right" id="submitAnswerFrmBtn">
                     </td>
                  </tr>
               <?php
               } else {
               ?>
                  <b>No questions available at this moment</b>
               <?php
               }
               ?>
            </table>
         </form>
      </div>
   </div>
</div>

<?php
function isImage($data)
{
    // You can implement a check based on the content or other criteria.
    // For example, if it's an image, it may start with a known image file signature.
    // You may need to adapt this check based on your actual data.
    
    // For simplicity, let's assume that if it starts with "data:image", it's an image.
    return strpos($data, 'data:image') === 0;
}
?>
</body>
</html>
