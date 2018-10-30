# Changelog

## 1.1
- Removed `Pimple` as dependency.
- Implemented `psr\container` for `WebPify\WebPify`.
- Introduced new Domain `App` and `App\Provider`.
- Replaced `Parser\RegExImageParser` with `ImageParser`
- Added compatibility with Gutenberg where `size-{size}` class from `<img>` is removed. 

## 1.0
- First stable release
- Moved to PSR-based coding standard.
- [[#1]](https://github.com/Chrico/webpify/issues/1) - Added new `AttachmentDeletor`

## 0.2.1
- Fixed bug where transform failed and `NULL` was used in `dirname()`.

## 0.2.0
- Added some more info to README.
- Use coverage-html.

## 0.1.0
- First "real release". :-)
- Moved to Brain\Monkey version 2.1.

## 0.0.1
- First release