# Accfarm WordPress plugin for Resellers 

This is a complete Accfarm Reseller plugin for WordPress with WooCommerce.
With this plugin you can successfully and easily resell accfarm account
just by installation and minor configuration. It is recommended to read this
guide fully before using the plugin.

- [Download](#Download)
- [Workflow](#Workflow)
- [Installation](#Installation)
  - [Authentication](#Authentication)
  - [Account Import](#Account-Import)
  - [Configure products](#Configure-products)
  - [Mail](#Mail)
  - [Errors](#Errors)
- [Development](#Development)
  - [Hooks](#Hooks)
  - [Testing](#Testing)
  - [Logs](#Logs)
- [What this plugin adds](#What-this-plugin-adds)

## Download

You can download plugin zip file here: 
[Download](https://github.com/ITAccfarm/accfarm-reseller-wp/releases/download/1.0.0/accfarm-reseller-1.0.0.zip)

## Workflow

Shop setup workflow:
1. WordPress WooCommerce installation.
2. [Install](#Installation) Accfarm Reseller plugin.
3. Type in your [Authentication](#Authentication) credentials.
4. Create Products either [manually](#Configure-products) or
[import them from accfarm](#Account-Import) and personalize them.
5. Add [Download Link and Delivery Time text to email](#Mail).

Customer cycle:
1. Customer goes to your website.
2. Chooses product and clicks _Add to cart_.

![image](https://user-images.githubusercontent.com/91614594/163560989-51ba2ef2-586d-482a-aa81-ff19c6cabd0f.png)

3. Proceeds to Checkout.

![image](https://user-images.githubusercontent.com/91614594/163561076-b427cd66-51c2-4aad-8355-341b7a1bdfe3.png)

4. Fills out the required data, clicks _Place order_ and pays.

![image](https://user-images.githubusercontent.com/91614594/163561223-ebc58b6c-644f-420e-b666-6e5ed23fd5d8.png)

5. When customer paid status sets to **Processing**. This will automatically
send a Buy request to Accfarm that will buy offer from your Accfarm balance.
You can change Order Status to when send accounts at [Hooks](#Hooks) 
settings page.

6. After that “Thank You” page greets user. It says one of 3 things:
  
Case: any Accfarm Product type and no delivery time: 
![image](https://user-images.githubusercontent.com/91614594/163563155-f1a6997c-9e91-44d6-b048-d0ce2ddec687.png)

Case: any Accfarm Product type with delivery time:
![image](https://user-images.githubusercontent.com/91614594/163563510-ef54517e-7036-4730-aec9-ef0ef72b8bc7.png)

Case: **offer** Accfarm Product type and download link ready:
![image](https://user-images.githubusercontent.com/91614594/163563935-6babef4a-140d-4c37-a7f5-fe09766f802d.png)

7. User receives an email with the same cases with texts that you provide at 
[Mail](#Mail) at the bottom.

![image](https://user-images.githubusercontent.com/91614594/163566136-f2f89502-3d05-4a17-b9ca-3ce2e4a5d6c4.png)

8. User also can see orders and download accounts in _My Account_ -> _Orders_
page.

![image](https://user-images.githubusercontent.com/91614594/163566281-d1e935f2-fea9-4585-bb16-e868f297ff6a.png)

9. When order on Accfarm's side was delivered callback comes from Accfarm
and sets final status on WooCommerce's order. "Complete" by default. 
This will send WooCommerce Complete email with details and in case 
of **offer** product type download link, as well as showing link
in _My Account_ -> _Orders_.

10. Cycle repeats.

  
## Installation
1. Install WordPress and WooCommerce
2. Go to _Plugins_ -> _Add New_ and click **Upload Plugin**

![image](https://user-images.githubusercontent.com/91614594/161974030-b68680fd-ff26-4f4e-962c-d5a44bc22c49.png)

3. Upload accfarm-reseller-\*.\*.\*.zip folder, where -\*.\*.\* is the version of plugin 
you are installing, in this case version is 1.0.0. After that click **Install Now**.

![image](https://user-images.githubusercontent.com/91614594/161974059-c88930b9-e7e3-44e4-b710-3a3c47901672.png)

4. After installation click Activate Plugin. 

![image](https://user-images.githubusercontent.com/91614594/161974089-ac78710c-72f0-4688-9d7a-2ba159a71c6c.png)

5. Go to _Settings_ -> _Permalinks_

![image](https://user-images.githubusercontent.com/91614594/162077060-6b826c32-9a36-4c1d-a399-1871cfec7f70.png)

6. Scroll to bottom and click **Save Changes**

![image](https://user-images.githubusercontent.com/91614594/162077221-0c71146a-0da0-4129-81c7-74caac5ba288.png)

7. Congratulations! You have successfully installed Accfarm Reseller plugin!

### Authentication

In order for Accfarm features to work you need to enter Login, Password and Secret 
in _Accfarm Reseller_ -> _General_.

![image](https://user-images.githubusercontent.com/91614594/162078212-d89e860d-fdbf-4a97-958c-a61d3da54131.png)

You can create Api Secret at https://accfarm.com/profile in the API section.

![image](https://user-images.githubusercontent.com/91614594/162078453-0616482d-0ba8-4854-9fcd-4bd9a39ffbb2.png)


### Account Import

You can import offers from Accfarm directly as a WordPress product!
For that you need to go to _Accfarm Reseller_ -> _Import_.

![image](https://user-images.githubusercontent.com/91614594/163555305-b6ec95fd-a419-4902-ae21-f1e28383d3c0.png)

1. You can choose if you want your WordPress product have published status or draft.
2. You can choose if you want to add prices from accfarm prices or not.
3. And if you add prices from accfarm you can automatically increase them for all
offers you import. And this can be a simple addition to the price or a percent of the price.

![image](https://user-images.githubusercontent.com/91614594/163555838-40844623-b0c6-413d-a161-524dda8ed0c8.png)

After you're pleased with the import settings you can choose the way you want to import accounts.

1. **Import all**. This will simply import All the Accfarm's offers, so be careful! This will also
take quite some time.

![image](https://user-images.githubusercontent.com/91614594/163556133-a50831f9-978c-477f-a26d-0481890d7af4.png)

2. **Import Category**. You can import all the Accfarm's category offers. Just choose the category
and press _Import Category Offers_.

![image](https://user-images.githubusercontent.com/91614594/163556368-6fbfe203-5f13-4163-9d39-90665744c645.png)

3. **Import Product**. If you press on any of the category you will see this Category's products.

![image](https://user-images.githubusercontent.com/91614594/163556581-ca014c56-311b-40ae-b54c-0e40e3c9a552.png)

![image](https://user-images.githubusercontent.com/91614594/163556639-f97b567a-4f4c-4285-a56c-61dd9f996fb4.png)

Then you can import all the Product's offers from category you have chosen by pressing the
_Import Products Offers_ button.

![image](https://user-images.githubusercontent.com/91614594/163556802-f39dc0d2-3d2b-4a75-bc5a-08497be2f18d.png)

4. **Import a single Offer**. Same way as before, you can press on Product, and it will lead you
to this Product's Offers.

![image](https://user-images.githubusercontent.com/91614594/163557018-7f4b3706-7a0d-4393-ad78-a38531b95901.png)

![image](https://user-images.githubusercontent.com/91614594/163557138-60b3d735-8628-4006-893c-af032afde98b.png)

Offers also show prices from accfarm, or, in the case you added margin to Accfarm's prices,
price with margin.

Addition to prices:
![image](https://user-images.githubusercontent.com/91614594/163557313-65d9c21a-2531-4119-86ad-3bc2b4d1936f.png)

Percent of the price:
![image](https://user-images.githubusercontent.com/91614594/163557424-4fa687fe-a349-45fc-97dc-6fd3fb033ef0.png)

To import single offer click _Import Offer_.

5. Or you can select Categories, Products or Offers you want to import with checkboxes 
and click _Import Selected_.

![image](https://user-images.githubusercontent.com/91614594/163558024-b1e62ee9-2330-4670-9ffa-47e165169b46.png)

This will lead you to the WooCommerce Products page.

![image](https://user-images.githubusercontent.com/91614594/163558232-368f7a9a-452c-4e0f-9766-5283f3238b85.png)

As you can see all offers were successfully imported from Accfarm with new prices! :)

### Configure products

When creating product in order for it to work as Accfarm product you need
to configure 2 fields.

1. Accfarm Offer ID: offer_id of accfarm offer.
2. Accfarm Product Type: product type of accfarm offer.

![image](https://user-images.githubusercontent.com/91614594/162083386-49b17cf9-37e7-43fb-b580-2ce2c6d912d2.png)

### Mail

After order created or when it's completed WooCommerce sends emails 
to the customers by default. You can find this list of mail templates in 
_WooCommerce_ -> _Settings_ -> _Emails_

![image](https://user-images.githubusercontent.com/91614594/163561864-ab245652-125c-48d1-a297-eb054bbc7cd7.png)

In WooCommerce, you can add so-called placeholders in mail template. They will be replaced 
by the things they represent, for example {site_url} will be replaced with your website url.

![image](https://user-images.githubusercontent.com/91614594/162079114-2dde92bc-1ddc-4a2e-a633-67ce37ba8054.png)

Accfarm Reseller plugin adds one placeholder called {accounts_link_or_delivery_time}.
You can add in WooCommerce in mail template. It basically writes either your text for when 
there is a link to download accounts or your delivery time text for customers order.
It will show only one option:
1. Accounts link text (if there is accounts link).
2. Delivery time text (if there is no accounts link and there is delivery time)
3. Nothing (if no Accounts link or Delivery time or not accounts in order)

As for these text, you have to add them by yourself in _Accfarm Reseller_ -> _Mail_.

![image](https://user-images.githubusercontent.com/91614594/162079330-800ac776-4b20-4d95-865c-210cd782e774.png)

## Errors

It may happen that some unexpected thing occur during Buy of the Accfarm accounts.
In this case order will get status _On Hold_, and you will receive an email with the problem.
For example:

![image](https://user-images.githubusercontent.com/91614594/163559821-cd92f8f1-22ee-4587-872a-6cfcf63a864f.png)

It will also show in WooCommerce Order

![image](https://user-images.githubusercontent.com/91614594/163567480-69543601-4b1e-4ad9-958f-ed6dbae723ff.png)

If you encounter an error like that or get in this kind of situation
you can change Order status back to **Processing** to send buy request
to Accfarm again.

![image](https://user-images.githubusercontent.com/91614594/163567587-e67d8d9f-dec1-4a46-a218-68687d0fc701.png)


## Development

This section is mainly for developers, so do any action with caution, 
as it could potentially break everything, if not operated carefully.

### Hooks

There are 2 main actions in this plugin. Send buy request to Accfarm and receive a callback.
"Buy" request will simply buy product from your Accfarm account and when the order is Completed
on Accfarm side Accfarm will send request back saying to your WordPress site that it's ready
or finished.

You can choose 2 statuses. When set buy and what order status to set after callback.
Thought it needs to be specified that, it is really **not recommended** to change them.
Go to _Accfarm Reseller_ -> _Hooks_. You can set statuses there.

![image](https://user-images.githubusercontent.com/91614594/162080308-b1efe9d0-568f-4a21-a23b-b2924476b46c.png)

1. On what order status to send buy request.
2. What status to set on callback.

### Testing

There are testing features for developers, but they need to be used carefully,
or it may have bad impact on the website or customers.

To use those features go to _Accfarm Reseller_ -> _Testing_.

![image](https://user-images.githubusercontent.com/91614594/162080983-c480faa2-250c-4844-8cc1-2bf149760ca6.png)

Here you can find 2 checkboxes.

1. Use sandbox (not real orders) will enable sandbox mode for Accfarm. This means when people
   buy Accfarm product from your website the products are not real and none of your money
   will be spent on Accfarm side.
2. Store logs will store responses from Buy request in _logs_ folder.

### Logs

If you enable logs on the testing page, then logs will be stored in
the wp-content/plugins/accfarm-reseller/logs/ folder. You can find them
at _Accfarm Reseller_ -> _Logs_.

![image](https://user-images.githubusercontent.com/91614594/163554797-4f8c37fd-752f-4437-9a0d-3cc9b16206d3.png)

Logs are stored for Buy, Order Update and Callback responses from Accfarm api.

## What this plugin adds

1. New data from Accfarm order to WooCommerce order. (On admin side.)

![image](https://user-images.githubusercontent.com/91614594/163561543-f516083c-a6e8-4658-b04f-638747957c46.png)

2. Accounts Link in WooCommerce _My Account_ order list. (On client side.)

![image](https://user-images.githubusercontent.com/91614594/162084124-4eb24929-a3b1-4ad8-817f-fa0458e8470b.png)

3. Link to download account in WooCommerce view order page. (On client side.)

![image](https://user-images.githubusercontent.com/91614594/163561627-764426fd-236e-48e9-a14d-eee98d859529.png)

4. New required field on checkout for different Accfarm Product types. (On client side.)

Reviews
![image](https://user-images.githubusercontent.com/91614594/163561698-109fbb92-f363-4442-b7cb-5436419f444f.png)

Installs
![image](https://user-images.githubusercontent.com/91614594/163561748-6f70dd9e-79d8-4105-b5ad-02d63028cade.png)

5. New data to WooCommerce Thank you page. (On client side.)
   
![image](https://user-images.githubusercontent.com/91614594/162085046-81e9afad-d1b5-45f3-a55d-cfc362823c7d.png)

![image](https://user-images.githubusercontent.com/91614594/162085294-76530171-de5a-4f82-b0db-6321ed84e46d.png)

6. Requires person to be registered or creates account on checkout to
buy products. (On client side.)
7. Allows only one item in shopping cart. (On client side.)