<?php

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Database\Database;
use Altum\Middlewares\Csrf;

class AdminPlanUpdate extends Controller {

    public function index() {

        $plan_id = isset($this->params[0]) ? $this->params[0] : null;

        /* Make sure it is either the trial / free plan or normal plans */
        switch($plan_id) {

            case 'free':

                /* Get the current settings for the free plan */
                $plan = settings()->plan_free;

                break;

            case 'trial':

                /* Get the current settings for the trial plan */
                $plan = settings()->plan_trial;

                break;

            default:

                $plan_id = (int) $plan_id;

                /* Check if plan exists */
                if(!$plan = db()->where('plan_id', $plan_id)->getOne('plans')) {
                    redirect('admin/plans');
                }

                /* Parse the settings of the plan */
                $plan->settings = json_decode($plan->settings);

                /* Parse the taxes */
                $plan->taxes_ids = json_decode($plan->taxes_ids);

                if(in_array(settings()->license->type, ['Extended License', 'extended'])) {
                    /* Get the available taxes from the system */
                    $taxes = db()->get('taxes', null, ['tax_id', 'internal_name', 'name', 'description']);
                }

                break;

        }

        if(!empty($_POST)) {

            if (!Csrf::check()) {
                Alerts::add_error(language()->global->error_message->invalid_csrf_token);
            }


            /* Determine the enabled biolink blocks */
            $enabled_biolink_blocks = [];

            foreach(require APP_PATH . 'includes/biolink_blocks.php' as $biolink_block) {
                $enabled_biolink_blocks[$biolink_block] = (bool) isset($_POST['enabled_biolink_blocks']) && in_array($biolink_block, $_POST['enabled_biolink_blocks']);
            }

            /* Filter variables */
            $_POST['settings'] = [
                'additional_global_domains' => (bool) isset($_POST['additional_global_domains']),
                'custom_url' => (bool) isset($_POST['custom_url']),
                'deep_links' => (bool) isset($_POST['deep_links']),
                'no_ads' => (bool) isset($_POST['no_ads']),
                'removable_branding' => (bool) isset($_POST['removable_branding']),
                'custom_branding' => (bool) isset($_POST['custom_branding']),
                'custom_colored_links' => (bool) isset($_POST['custom_colored_links']),
                'statistics' => (bool) isset($_POST['statistics']),
                'custom_backgrounds' => (bool) isset($_POST['custom_backgrounds']),
                'verified' => (bool) isset($_POST['verified']),
                'temporary_url_is_enabled' => (bool) isset($_POST['temporary_url_is_enabled']),
                'seo' => (bool) isset($_POST['seo']),
                'utm' => (bool) isset($_POST['utm']),
                'socials' => (bool) isset($_POST['socials']),
                'fonts' => (bool) isset($_POST['fonts']),
                'password' => (bool) isset($_POST['password']),
                'sensitive_content' => (bool) isset($_POST['sensitive_content']),
                'leap_link' => (bool) isset($_POST['leap_link']),
                'api_is_enabled' => (bool) isset($_POST['api_is_enabled']),
                'projects_limit' => (int) $_POST['projects_limit'],
                'pixels_limit' => (int) $_POST['pixels_limit'],
                'biolinks_limit' => (int) $_POST['biolinks_limit'],
                'links_limit' => (int) $_POST['links_limit'],
                'domains_limit' => (int) $_POST['domains_limit'],
                'enabled_biolink_blocks' => $enabled_biolink_blocks,
            ];

            switch($plan_id) {

                case 'free':

                    $_POST['name'] = Database::clean_string($_POST['name']);
                    $_POST['status'] = (int)$_POST['status'];

                    /* Make sure to not let the admin disable ALL the plans */
                    if(!$_POST['status']) {

                        $enabled_plans = (int) settings()->payment->is_enabled ? database()->query("SELECT COUNT(*) AS `total` FROM `plans` WHERE `status` = 1")->fetch_object()->total ?? 0 : 0;

                        if(!$enabled_plans && !settings()->plan_trial->status) {
                            Alerts::add_error(language()->admin_plan_update->error_message->disabled_plans);
                        }
                    }

                    $setting_key = 'plan_free';
                    $setting_value = json_encode([
                        'plan_id' => 'free',
                        'name' => $_POST['name'],
                        'days' => null,
                        'status' => $_POST['status'],
                        'settings' => $_POST['settings']
                    ]);

                    break;

                case 'trial':

                    $_POST['name'] = Database::clean_string($_POST['name']);
                    $_POST['days'] = (int)$_POST['days'];
                    $_POST['status'] = (int)$_POST['status'];

                    /* Make sure to not let the admin disable ALL the plans */
                    if(!$_POST['status']) {

                        $enabled_plans = (int) settings()->payment->is_enabled ? database()->query("SELECT COUNT(*) AS `total` FROM `plans` WHERE `status` = 1")->fetch_object()->total ?? 0 : 0;

                        if(!$enabled_plans && !settings()->plan_free->status) {
                            Alerts::add_error(language()->admin_plan_update->error_message->disabled_plans);
                        }
                    }

                    $setting_key = 'plan_trial';
                    $setting_value = json_encode([
                        'plan_id' => 'trial',
                        'name' => $_POST['name'],
                        'days' => $_POST['days'],
                        'status' => $_POST['status'],
                        'settings' => $_POST['settings']
                    ]);

                    break;

                default:

                    $_POST['name'] = Database::clean_string($_POST['name']);
                    $_POST['monthly_price'] = (float) $_POST['monthly_price'];
                    $_POST['annual_price'] = (float) $_POST['annual_price'];
                    $_POST['lifetime_price'] = (float) $_POST['lifetime_price'];
                    $_POST['status'] = (int) $_POST['status'];
                    $_POST['taxes_ids'] = json_encode(array_keys($_POST['taxes_ids'] ?? []));

                    /* Make sure to not let the admin disable ALL the plans */
                    if(!$_POST['status']) {

                        $enabled_plans = (int) database()->query("SELECT COUNT(*) AS `total` FROM `plans` WHERE `status` = 1")->fetch_object()->total ?? 0;

                        if(
                            (
                                !$enabled_plans ||
                                ($enabled_plans == 1 && $plan->status))
                            && !settings()->plan_free->status
                            && !settings()->plan_trial->status
                        ) {
                            Alerts::add_error(language()->admin_plan_update->error_message->disabled_plans);
                        }
                    }

                    break;

            }


            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Update the plan in database */
                switch ($plan_id) {

                    case 'free':
                    case 'trial':

                        db()->where('`key`', $setting_key)->update('settings', ['value' => $setting_value]);

                        /* Clear the cache */
                        \Altum\Cache::$adapter->deleteItem('settings');

                        break;

                    default:

                        $settings = json_encode($_POST['settings']);

                        db()->where('plan_id', $plan_id)->update('plans', [
                            'name' => $_POST['name'],
                            'monthly_price' => $_POST['monthly_price'],
                            'annual_price' => $_POST['annual_price'],
                            'lifetime_price' => $_POST['lifetime_price'],
                            'settings' => $settings,
                            'taxes_ids' => $_POST['taxes_ids'],
                            'status' => $_POST['status'],
                        ]);

                        break;

                }

                /* Update all users plan settings with these ones */
                if(isset($_POST['submit_update_users_plan_settings'])) {

                    $plan_settings = json_encode($_POST['settings']);

                    db()->where('plan_id', $plan_id)->update('users', ['plan_settings' => $plan_settings]);

                    /* Clear the cache */
                    \Altum\Cache::$adapter->deleteItemsByTag('users');

                }

                /* Set a nice success message */
                Alerts::add_success(language()->global->success_message->basic);

                /* Refresh the page */
                redirect('admin/plan-update/' . $plan_id);

            }

        }

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/plans/plan_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Main View */
        $data = [
            'plan_id'    => $plan_id,
            'plan'       => $plan,
            'taxes'      => $taxes ?? null
        ];

        $view = new \Altum\Views\View('admin/plan-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
