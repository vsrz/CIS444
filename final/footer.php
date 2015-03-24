<!-- begin footer.php -->
	</div>
	<div class="container"><img src="images/img03.png" width="1000" height="40" alt="footer" /></div>
</div>
<div id="footer-content"></div>
<div id="footer">
	<p>Copyright (c) 2012 checkitout.com. All rights reserved. </p>
</div>

<!-- Google Analytics -->
<script>
    <?php
        if (isset($_GET['context'])) {
            echo "fieldObject.dimension1 = '".$_GET['context']."';";
        }
        echo "ga('set', fieldObject);";
        echo "ga('send', 'pageview');";
    ?>
</script>
</body>
</html>
