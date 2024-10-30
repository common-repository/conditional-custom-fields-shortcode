=== Conditional Custom Fields Shortcode ===
Contributors: oltdev, godfreykfc 
Tags: conditional, custom fields, shortcode, template, CMS
Requires at least: 2.7
Tested up to: 2.8.2
Stable tag: trunk

Use custom field values in you pages or posts. With conditional supports which enables basic templating with custom fields.

== Description ==
A series of shortcodes for using custom field values in pages or posts (or in sidebar widgets - via [Section Widget][1]!), which would then allows you to take store the actual data in custom fields while storing the presentation in the page/post body. This transforms WordPress into an awesome CMS. Keep reading for more inspirations.

= Basic Usage =

`[cf "custom field name"]`

Example:

    Picture of the day:
    <img src='[cf "picture-link"]' />

This would output something like `<img src='http://thesite.com/something.jpg' />`, assume the post/page has the custom field "picture-link" and its value is "http://thesite.com/something.jpg". That way, you (or your clients!) would never accidentally take out a div when updating your page because you won't have to touch the page/post body anymore!

= Default Values =

`[cf "custom field name" default="display this if the CF is undefined"]`

Example:

    Contact [cf "support-email" default="support@mycompany.com"] if you have questions.

support@mycompany.com will be displayed there if the custom field "support-email" is not present in that post/page.

= Simple Template =

`[cf "custom field name" default="default value"]...template...[/cf]`

Example:

    Price: [cf "price" default="Pricing information not available yet."] USD$%value% [/cf]

If you cannot use `%value%` as your placeholder text for some reason, you can change it by specifying the placeholder attribute.

Example:

    Price: [cf "price" default="Pricing information not available yet." placeholder="__value__"] USD$__value__ [/cf]

= Multiple Values =

It's smart enough to do the right thing. If your post has the following custom fields:

*   concert-date: Jan 14th
*   concert-date: Jan 28th
*   concert-date: Feb 2nd

Then `[cf "concert-date"]` will output `Jan 14th,Jan 28th,Feb2nd`, exactly what you would expect. You can also optionally provide a separator to replace the default (,).

Example:

    Come to one of our concerts on the following dates: [cf "concert-date" separator=" / "]

(=> Jan 14th / Jan 28th / Feb 2nd)

= Sorting =

`[cf "custom field name" sort="nosort|asc|dsc|random" type="string|int|integer|float|bool|boolean|date|auto"]`

You can control how it sort the items (when there's more than one) using the sort attribute (default is `nosort`). By default, it would compare them using PHP's comparison operator (i.e. try to cast them into numeric values, or compare them as strings if that failed). However, you can force a type cast by specifying the type attribute (default is `auto`).

= Display Single Value =

If display multiple values is not what you want it to do, you can use `[cf single="true"]` to force it to display only the first value. Combining what we have learned so far, you can display a single random value by `[cf single="true" sort="random"]`.

= Summary =

Syntax for `[cf]` shortcode: (default values in parenthesis)

    [cf "field-name" default="" placeholder="%value%" single="(false)|true" separator="," sort="(nosort)|asc|dsc|random" type="string|int|integer|float|bool|boolean|date|(auto)"](OPTIONAL) template[/cf]
    
= Conditionals =

Conditionals tags is what makes this plugin unique. Here is a list of them:

    [if-cf-def "custom field name"]
      Do this when "custom field name" is defined for this page/post
    [/if-cf-def]

    [if-cf-ndef "custom field name"]
      Do this when "custom field name" is NOT defined for this page/post
    [/if-cf-ndef]

    [if-cf-eq "custom field name" "value"]
      Do this when "custom field name" == "value"
    [/if-cf-eq]

    [if-cf-neq "custom field name" "value"]
      Do this when "custom field name" != "value"
    [/if-cf-neq]

    [if-cf-lt "custom field name" "value"]
      Do this when "custom field name" < "value"
    [/if-cf-lt]

    [if-cf-gt "custom field name" "value"]
      Do this when "custom field name" > "value"
    [/if-cf-gt]

    [if-cf-let "custom field name" "value"]
      Do this when "custom field name" <= "value"
    [/if-cf-let]

    [if-cf-get "custom field name" "value"]
      Do this when "custom field name" >= "value"
    [/if-cf-get]

More detailed documentations will be published soon. In the mean time, check out the following examples and the comments in the PHP code to get a rough idea.

= Examples =

    [if-cf-get "deadline" "today" type="date"]
        Submit your application <a href='[cf "submit-link"]'>here</a>.
    [/if-cf-get]

    [if-cf-lt "deadline" "today" type="date"]
        Sorry we regret that we cannot take anymore applications.
    [/if-cf-lt]

    [if-cf-def "discounted-price" "discount-expires" logic="and"]
        This product is on discount, get it now for [cf "discounted-price" /] [cf "discount-expires" default="for a limited time"]before %value%[/cf]!
    [/if-cf-def]


**Please note: It seems that there is a serious bug in WordPress' shortcode parser that prevents shortcodes from functioning correctly in certain occasions.** The developers are hard at work to get this fixed before the 2.8.3 release. (See [#10082][2]) Therefore, please do not report any parser related bugs for the moment. (e.g. the shortcode is displayed on the actual page)

 [1]: http://wordpress.org/extend/plugins/widget-logic/
 [2]: http://core.trac.wordpress.org/ticket/10082

== Installation ==

1. Extract the zip file and drop the contents in the wp-content/plugins/ directory of your WordPress installation
2. Activate the Plugin from Plugins page


== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

