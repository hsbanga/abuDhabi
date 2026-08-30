# WhatsyCart

A WooCommerce plugin that sends WhatsApp notifications for order events using the WhatsApp Business Cloud API (Meta).

## Features

- Notify the store admin's WhatsApp number when a new order is placed
- Notify the customer via WhatsApp when their order status changes
- Settings page under **WooCommerce → WhatsyCart** for API credentials and notification toggles

## Requirements

- WordPress with WooCommerce active
- A WhatsApp Business Cloud API account (phone number ID + access token) from [Meta for Developers](https://developers.facebook.com/docs/whatsapp/cloud-api)

## Setup

1. Copy the `whatsycart` folder into `wp-content/plugins/`.
2. Activate **WhatsyCart** from the Plugins screen.
3. Go to **WooCommerce → WhatsyCart** and enter your WhatsApp Phone Number ID and Access Token.
4. Set the admin notification number and toggle which notifications you want enabled.

## Project structure

```
whatsycart/
├── whatsycart.php                              # Plugin bootstrap
├── includes/
│   ├── class-whatsycart-settings.php           # Settings page + option storage
│   ├── class-whatsycart-api.php                # WhatsApp Cloud API client
│   └── class-whatsycart-order-notifications.php # WooCommerce hooks
├── admin/                                      # Reserved for future admin UI assets
└── assets/                                     # JS/CSS assets
```

## Notes

Recipient numbers must include country code and no leading `+` or spaces (e.g. `15551234567`), per the WhatsApp Cloud API's requirements.
