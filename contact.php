<?php require_once('header.php'); ?>
<div id="page">
    <div id="content_text">
        <h1 class="title"><img src="images/phone.png" class="icon64" alt="&nbsp;" />Contact Us</h1>
        <div class="clear">&nbsp;</div>
        <div class="line">&nbsp;</div>
        <p>
            Questions or concerns? Please feel free to write us
            a message and one of our staff will get back to you as
            soon as possible.

            For link reporting, we will not reply once a decision
            has been made.
        </p>
        <div class="clear">&nbsp;</div>
        <div id="contact_form">
            <form action="#" method="POST" onsubmit="return contactValidate()">
                <label for="name">Name</label>
                <input type="text" class="txt" id="name" size="45" name="name" onblur="contactCheckValid(this)" value="" />
                <label for="email">E-mail</label>
                <input type="text" class="txt" id="email" size="45" onblur="contactCheckValid(this)" name="email" value="" />
                <label for="message">Message</label>
                <textarea rows="8" class="txt" cols="70" onblur="contactCheckValid(this)" id="message"></textarea>
                <input type="submit" class="button orange" id="submit" value="Submit" />
                <input type="button" class="button orange" value="Clear" onclick="clearContactForm()" />
            </form>
        </div>
    </div>
<?php require_once('footer.php'); ?>
