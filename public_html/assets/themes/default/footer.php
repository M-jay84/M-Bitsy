         <?php column('middle'); ?>
        </div>
       <!-- Right Block Start -->
      <div class="col  portal_sidebar">
        <?php column('right'); ?>
      </div>
    </div>
   </div>
<!-- Content End -->

<!-- Footer Start -->
<footer class="mt-auto">
    <div class="tt_block rounded p-4">
        <div class="row">
            <div class="col-12 col-sm-6 text-center text-sm-start"><br>
                &copy; <a href="<?= Url() ?>"><?= CONFIG['SITENAME'] ?></a><br>
                RSS <a href="<?= Url('rss') ?>"><i class="fa fa-rss" aria-hidden="true" title="RSS Feed" style="color:#ff9900;"></i></a>
                <a href="<?= Url('rss/custom') ?>"><i class="fa fa-rss-square" aria-hidden="true" title="Custom RSS" style="color:#ffff00;"></i></a>
            </div>
            <div class="col-12 col-sm-6 text-center text-sm-end">
                M-BiTsy By <a href="https://github.com/M-jay84/M-BiTsy">M-jay</a>
                <br>Support @ <a href="https://torrenttrader.uk" target="_blank">Torrent Trader</a><br>
                <?php $totaltime = array_sum(explode(" ", microtime())) - $GLOBALS['tstart']; ?>
                <?php printf(LANG["PAGE_GENERATED_IN"], $totaltime); ?>
            </div>
        </div>
    </div>
</footer>
<!-- Footer End -->

<!-- Jquery/Bootstrap Libraries -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Smileys/CheckAll JS  -->
<script src="<?= Url('assets/js/smileIT_checkall.js') ?>"></script>
<!-- Google Captcha JS  -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<!-- Highlight Code JS  -->
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/highlight.min.js"></script>
<script>  hljs.initHighlightingOnLoad(); </script>
<!-- Pop-up Ballon JS  -->
<script src="<?= Url('assets/js/overlib.js') ?>"></script>
<!-- Tags JS  -->
<script type="module"> import Tags from "<?= Url('assets/js/tags.js') ?>"; Tags.init("select[multiple]"); </script>
<!-- Shoutbox JS  -->
<?php require "assets/js/shoutbox.js"; ?>
<!-- Carousel JS  -->
<?php require "assets/js/carousel.js"; ?>
<!-- Message UserSearch JS -->
<?php require "assets/js/usersearch.js"; ?>

</body>

</html>
<?php ob_end_flush(); ?>