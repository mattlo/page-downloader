# Page Downloader
This is a utility program that will download web pages with assets based on provided absolute URLs.
Downloads:
- HTML with preserved extension (e.g.: /path/to/page.aspx saves as page.aspx)
- CSS (from `<link />`s)
- Assets using the `url` method in all CSS files
- JavaScript (from `<script />`s)
- Images (from `<img />`s)

#### File Organization
The utility will identify global vs non-global assets.

#### How it works under the hood
JavaScript acts as the manager and the server is given tasks to download individual pages. Having the client handle delegation 
enables easier modifications without the high chance of breaking the core utility.

**Note:** Make sure the root directory has read, write, and execute access or the script might fail due to file system permissions.

## Environment

### Server Dependencies
- Apache 2.2.22
- PHP 5.4.4

### Client Dependencies
- jQuery 2.0.3
- Twitter Bootstrap 3.0.0

## Notes
Initially this was intended to be a CLI tool but having a user interface allows developers to 
just throw this tool at their non-technical coworkers.