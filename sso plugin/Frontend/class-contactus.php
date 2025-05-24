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
        <div class="bpc-sso-contact-us">
            <button type="button" class="bpc-sso-contact-us-button" id="bpc_sso_contact_us_button"><img width="30px" height="30px" src="<?php echo bpcwrapper::bpc_sso_get_image_url('contact-us.svg') ?>"/></button>
            <div class="bpc_sso_contact_us_dialogue" id="bpc_sso_contact_us_dialogue">
                Reach out to us for any inquiry!
            </div>
            <div class="contact-form-container" id="bpc-sso-contact-us-form" style="display:none;">
                <div class="contact-form-header">
                    <img src="<?php echo bpcwrapper::bpc_sso_get_image_url('contact-us-header.svg') ?>" alt="Contact Icon" class="contact-icon" />
                    <h2>Contact Us</h2>
                    <p>Complete the form, and we'll follow up shortly.</p>
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
            const contactButton = document.getElementById('bpc_sso_contact_us_button');
            const contactForm = document.getElementById('bpc-sso-contact-us-form');
            const dialogue = document.getElementById('bpc_sso_contact_us_dialogue');

            let dialogueTimeout;

            function toggleForm() {
                const isHidden = contactForm.style.display === 'none';

                if (isHidden) {
                    contactForm.style.display = 'block';
                    contactButton.innerHTML = '<img width="34px" height="34px" src="<?php echo bpcwrapper::bpc_sso_get_image_url('close-x.svg') ?>"/>';
                    dialogue.style.display = 'none';
                    clearTimeout(dialogueTimeout);
                } else {
                    contactForm.style.display = 'none';
                    contactButton.innerHTML = '<img width="34px" height="34px" src="<?php echo bpcwrapper::bpc_sso_get_image_url('contact-us.svg') ?>"/>';
                    startDialogueTimer();
                }
            }

            function startDialogueTimer() {
                clearTimeout(dialogueTimeout);
                dialogueTimeout = setTimeout(() => {
                    dialogue.style.display = 'block';
                }, 5000); // 5 seconds delay
            }

            contactButton.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleForm();
            });

            // Close the form when clicking outside
            document.addEventListener('click', function (e) {
                const isClickInside = contactForm.contains(e.target) || contactButton.contains(e.target);

                if (!isClickInside && contactForm.style.display === 'block') {
                    contactForm.style.display = 'none';
                    contactButton.innerHTML = '<img width="34px" height="34px" src="<?php echo bpcwrapper::bpc_sso_get_image_url('contact-us.svg') ?>"/>';
                    startDialogueTimer();
                }
            });
        </script>

    <?php
    }
}
