{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *}

<!-- Details-->
<h3>{l s='Order Tracking' mod='glsordertracker'}</h3>
<!-- Progress-->
<div class="steps">
    <div class="steps-header">
        {assign var="width_map" value=[3 => '50%', 4 => '83.33%', 5 => '100%']}
        {assign var="width" value=$width_map[$tracker.orderStatusId]|default:'0%'}
        {assign var="aria_valuenow" value=$width_map[$tracker.orderStatusId]|default:'0'}

        <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                style="width: {$width}" aria-valuenow="{$aria_valuenow}" aria-valuemin="0" aria-valuemax="100">
            </div>
        </div>
    </div>
    <div class="steps-body">
        <div
            class="step {if $tracker.orderStatusId == 3 || $tracker.orderStatusId == 4 || $tracker.orderStatusId == 5}step-completed{/if}">
            {if $tracker.orderStatusId == 3 || $tracker.orderStatusId == 4 || $tracker.orderStatusId == 5}
                <span class="step-indicator">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-check" style="fill: none">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
            {/if}
            <span class="step-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-settings" style="fill: none">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path
                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                    </path>
                </svg>
            </span>
            {l s='Processing order' mod='glsordertracker'}
        </div>
        <div
            class="step {if $tracker.orderStatusId == 4 || $tracker.orderStatusId == 5}step-completed{/if} {if $tracker.orderStatusId == 3}step-active{/if}">
            {if $tracker.orderStatusId == 4 || $tracker.orderStatusId == 5}
                <span class="step-indicator">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-check" style="fill: none">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
            {/if}
            <span class="step-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-truck" style="fill: none">
                    <rect x="1" y="3" width="15" height="13"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
            </span>
            {l s='Shipped' mod='glsordertracker'}
        </div>
        <div
            class="step {if $tracker.orderStatusId == 5}step-completed{/if} {if $tracker.orderStatusId == 4}step-active{/if}">
            {if $tracker.orderStatusId == 5}
                <span class="step-indicator">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-check" style="fill: none">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </span>
            {/if}
            <span class="step-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="feather feather-home" style="fill: none">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </span>
            {l s='Delivered' mod='glsordertracker'}
        </div>
    </div>
</div>
<!-- Footer-->
<div class="d-sm-flex flex-wrap justify-content-between align-items-center text-center pt-4">
    <a id="trackButton" class="btn btn-primary btn-sm mt-2" href="#order-tracking" data-toggle="modal">{l s='View Tracking
        Details' mod='glsordertracker'}</a>
</div>

<!-- Modal Structure -->
<div class="modal fade" id="order-tracking" tabindex="-1" role="dialog" aria-labelledby="orderTrackingLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="orderTrackingLabel">{l s='Tracking Details' mod='glsordertracker'}</h5>
            </div>
            <div class="modal-body">
                <p id="trackingInfo">{l s='Loading...' mod='glsordertracker'}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close' mod='glsordertracker'}</button>
            </div>
        </div>
    </div>
</div>

{literal}
    <script>
        function executeTrackingScript() {
            $(document).ready(function() {
                $('#trackButton').click(function() {
                    $.ajax({
                        url: '{/literal}{$ajaxLink}{literal}',
                        cache: false,
                        type: 'POST',
                        data: {
                            ajax: true,
                            action: 'trackOrder',
                            shippingNumber: '{/literal}{$tracker.shippingNumber}{literal}',
                        },
                        success: function(response) {
                            const data = JSON.parse(response);
                            if (data.success) {
                                const trackingData = data.trackingData;
                                const lastTracking = data.lastTracking;
                                const city = data.city;
                                const coordinates = data.coordinates;

                                let html = '<div class="timeline">';
                                trackingData.forEach(tracking => {
                                    html += `
        <div class="timeline-item">
            <div class="timeline-content">
<h6 class="timeline-title">${tracking.status_desc}</h6>
<p class="timeline-date text-muted">${tracking.date} at ${tracking.time}</p>
<p class="timeline-location">${tracking.city}</p>
${tracking.note ? `<p class="timeline-note text-muted">${tracking.note}</p>` : ''}
${tracking.status_desc === lastTracking.status_desc ? `
    ${coordinates ? `
                        <div class="timeline-map mt-1 text-center">
<img class="img-fluid rounded" src="https://maps.geoapify.com/v1/staticmap?style=maptiler-3d&width=300&height=300&center=lonlat:${coordinates[0]},${coordinates[1]}&zoom=14&marker=lonlat:${coordinates[0]},${coordinates[1]};color:%23ff0000;size:medium&apiKey=1f36133f60b4441098742d4ddf4009a5" alt="Map of ${city}">
                        </div>
                    ` : `
<div class="alert alert-warning mt-3">Unable to fetch coordinates for ${city}</div>
                    `}
                    ` : ''}
            </div>
        </div>`;
                                });
                                html += '</div>';

                                $('#trackingInfo').html(html);
                            } else {
                                $('#trackingInfo').html(data.message);
                            }
                        },
                        error: function() {
                            $('#trackingInfo').text('Error retrieving tracking information.');
                        }
                    });
                });
            });
        }

        function checkJQuery() {
            if (window.jQuery) {
                $(document).ready(executeTrackingScript);
            } else {
                setTimeout(checkJQuery, 100);
            }
        }

        checkJQuery();
    </script>
{/literal}

<style>
    .steps {
        border: 1px solid #e7e7e7
    }

    .steps-header {
        padding: .375rem;
        border-bottom: 1px solid #e7e7e7
    }

    .steps-header .progress {
        height: .5rem
    }

    .steps-body {
        display: table;
        table-layout: fixed;
        width: 100%
    }

    .step {
        display: table-cell;
        position: relative;
        padding: 1rem .75rem;
        -webkit-transition: all 0.25s ease-in-out;
        transition: all 0.25s ease-in-out;
        border-right: 1px dashed #dfdfdf;
        color: rgba(0, 0, 0, 0.65);
        font-weight: 600;
        text-align: center;
        text-decoration: none
    }

    .step:last-child {
        border-right: 0
    }

    .step-indicator {
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        top: .75rem;
        left: .75rem;
        width: 1.5rem;
        height: 1.5rem;
        border: 1px solid #e7e7e7;
        border-radius: 50%;
        background-color: #fff;
        font-size: .875rem;
        line-height: 1.375rem
    }

    .has-indicator {
        padding-right: 1.5rem;
        padding-left: 2.375rem
    }

    .has-indicator .step-indicator {
        top: 50%;
        margin-top: -.75rem
    }

    .step-icon {
        display: block;
        width: 1.5rem;
        height: 1.5rem;
        margin: 0 auto;
        margin-bottom: .75rem;
        -webkit-transition: all 0.25s ease-in-out;
        transition: all 0.25s ease-in-out;
        color: #888
    }

    .step:hover {
        color: rgba(0, 0, 0, 0.9);
        text-decoration: none
    }

    .step:hover .step-indicator {
        -webkit-transition: all 0.25s ease-in-out;
        transition: all 0.25s ease-in-out;
        border-color: transparent;
        background-color: #f4f4f4
    }

    .step:hover .step-icon {
        color: rgba(0, 0, 0, 0.9)
    }

    .step-active,
    .step-active:hover {
        color: rgba(0, 0, 0, 0.9);
        pointer-events: none;
        cursor: default
    }

    .step-active .step-indicator,
    .step-active:hover .step-indicator {
        border-color: transparent;
        background-color: #5c77fc;
        color: #fff
    }

    .step-active .step-icon,
    .step-active:hover .step-icon {
        color: #5c77fc
    }

    .step-completed .step-indicator,
    .step-completed:hover .step-indicator {
        border-color: transparent;
        background-color: rgba(51, 203, 129, 0.12);
        color: #33cb81;
        line-height: 1.25rem
    }

    .step-completed .step-indicator .feather,
    .step-completed:hover .step-indicator .feather {
        width: .875rem;
        height: .875rem
    }

    @media (max-width: 575.98px) {
        .steps-header {
            display: none
        }

        .steps-body,
        .step {
            display: block
        }

        .step {
            border-right: 0;
            border-bottom: 1px dashed #e7e7e7
        }

        .step:last-child {
            border-bottom: 0
        }

        .has-indicator {
            padding: 1rem .75rem
        }

        .has-indicator .step-indicator {
            display: inline-block;
            position: static;
            margin: 0;
            margin-right: 0.75rem
        }
    }

    .bg-secondary {
        background-color: #f7f7f7 !important;
    }

    .progress-bar-animated {
        -webkit-animation: progress-bar-stripes 1s linear infinite;
        animation: progress-bar-stripes 1s linear infinite;
    }

    .progress-bar-striped {
        background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
        background-size: 1rem 1rem;
    }

    .progress-bar {
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        background-color: #25b9d7;
        -webkit-transition: width .6s ease;
        transition: width .6s ease;
    }

    .progress {
        height: 1rem;
        margin-bottom: 0;
        font-size: .65625rem;
        background-color: #fafbfc;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 .1rem .1rem rgba(0, 0, 0, .1);
        box-shadow: inset 0 .1rem .1rem rgba(0, 0, 0, .1);
    }

    .progress,
    .progress-bar {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        overflow: hidden;
    }

    /* Time line */
    .timeline {
        position: relative;
        padding: 20px 0;
        list-style: none;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-content {
        padding: 10px 20px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    .timeline-title {
        font-weight: bold;
    }

    .timeline-date,
    .timeline-location,
    .timeline-note {
        margin-bottom: 5px;
    }
</style>