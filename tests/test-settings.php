<?php
/**
 * Class SampleTest
 *
 * @package _bideja_Mailchimp
 */

/**
 * Sample test case.
 */
class OptionsTest extends WP_UnitTestCase
{

    /**
     * Testing settings fields
     */
    public function test_options_data()
    {
        $random_string = rand_str();

        // Test API key
        $this->assertFalse(get_option('mailchimp_api_key'));
        $this->assertTrue(add_option('mailchimp_api_key', $random_string));
        $this->assertTrue(get_option('mailchimp_api_key') === $random_string);

        // Test list ID
        $this->assertFalse(get_option('mailchimp_list_id'));
        $this->assertTrue(add_option('mailchimp_list_id', $random_string));
        $this->assertTrue(get_option('mailchimp_list_id') === $random_string);
    }
}
