<?php

namespace BPCSSO\Frontend\SAML;

use BPCSSO\Helper\bpcwrapper;

class ContactUs
{
    private static $instance;

    public static function get_instance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function bpc_sso_render_contact_us_form()
    {
    ?>
        <!-- <div class="bpc-sso-contact-us">
            <button type="button" class="bpc-sso-contact-us-button"><img width="30px" height="30px" src="<?php echo bpcwrapper::bpc_sso_get_image_url('contact-us.svg') ?>"/></button>
            <form action="" method="post" class="form" id="bpc-sso-contact-us-form" style="display:none;">
                <header class="bpc_sso_admin_front_header">
                    <h3>Contact Us</h3>
                    <img class="bpc_sso_admin_front_header_img" src="<?php echo bpcwrapper::bpc_sso_get_image_url('contact-us-header.svg') ?>"/>
                </header>

                <input hidden name="option" value="bpc_sso_contact_us" />
                <input hidden name="tab" value="contact-us" />
                <?php echo wp_nonce_field('bpc_sso_contact_us'); ?>

                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Your Name" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Your Email" required>

                <label for="message">Message:</label>
                <textarea id="message" name="message" placeholder="Your Message" required></textarea>

                <input type="submit" value="Submit">
            </form>
        </div> -->

        <div class="bpc-sso-contact-us">
            <button type="button" class="bpc-sso-contact-us-button" id="bpc_sso_contact_us_button"><img width="30px" height="30px" src="<?php echo bpcwrapper::bpc_sso_get_image_url('contact-us.svg') ?>"/></button>
            <div class="contact-form-container" id="bpc-sso-contact-us-form" style="display:none;">
                <div class="contact-form-header">
                    <img src="<?php echo bpcwrapper::bpc_sso_get_image_url('contact-us-header.svg') ?>" alt="Contact Icon" class="contact-icon" />
                    <h2>Contact Us</h2>
                    <p>Reach out to us for any inquiry</p>
                </div>
                <form enctype="multipart/form-data" action="" method="post">
                    <input hidden name="option" value="bpc_sso_contact_us" />
                    <input hidden name="tab" value="contact-us" />
                    <?php echo wp_nonce_field('bpc_sso_contact_us'); ?>

                    <input type="text" name="full_name" placeholder="Full name" required />
                    <input type="email" name="email" placeholder="Email" required />
                    <input type="tel" name="phone_number" placeholder="Phone Number" />
                    <textarea name="message" rows="4" placeholder="Message" required></textarea>

                    <button type="submit" class="send-btn">Send</button>
                </form>
            </div>
        </div>


        <script type="text/javascript">
            document.querySelector('.bpc-sso-contact-us-button').addEventListener('click', function() {
                var form = document.getElementById('bpc-sso-contact-us-form');
                var button = document.getElementById('bpc_sso_contact_us_button');
                if (form.style.display === 'none') {
                    form.style.display = 'block';
                    button.innerHTML = '<img width="30px" height="30px" src="<?php echo bpcwrapper::bpc_sso_get_image_url('close-x.svg') ?>"/>';
                } else {
                    form.style.display = 'none';
                    button.innerHTML = '<img width="30px" height="30px" src="<?php echo bpcwrapper::bpc_sso_get_image_url('contact-us.svg') ?>"/>';
                }
            });
        </script>
    <?php
    }
}
