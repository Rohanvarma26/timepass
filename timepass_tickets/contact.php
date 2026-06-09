<?php
include_once "header.php"; // Include the navbar
?>

<div class="container py-5">
    <div class="p-5 rounded" style="background-color: rgba(0, 0, 0, 0.85); border: 1px solid #00ffd5; box-shadow: 0 0 15px rgba(0, 255, 213, 0.5);">
        <h2 class="text-center mb-4" style="color: #00ffd5; text-shadow: 0 0 8px rgba(0, 255, 213, 0.7);">Contact Us</h2>
        <p class="lead text-center" style="color: #e0e0e0;">Have questions or need help? Feel free to reach out to us.</p>

        <form action="send_message.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label" style="color: #00ffd5;">Your Name</label>
                <input type="text" class="form-control" id="name" name="name" required 
                       style="background: rgba(255, 255, 255, 0.1); color: white; border: 1px solid #00ffd5;">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label" style="color: #00ffd5;">Your Email</label>
                <input type="email" class="form-control" id="email" name="email" required 
                       style="background: rgba(255, 255, 255, 0.1); color: white; border: 1px solid #00ffd5;">
            </div>
            <div class="mb-3">
                <label for="message" class="form-label" style="color: #00ffd5;">Your Message</label>
                <textarea class="form-control" id="message" name="message" rows="4" required 
                          style="background: rgba(255, 255, 255, 0.1); color: white; border: 1px solid #00ffd5;"></textarea>
            </div>
            <button type="submit" class="btn w-100" style="
                background-color: #4895ef;
                color: white;
                border: none;
                transition: background-color 0.3s, transform 0.2s;
                padding: 12px;
                border-radius: 6px;
            ">Send Message</button>
        </form>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>

<?php
include_once "footer.php"; // Include the footer
?>
