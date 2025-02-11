This document will guide you through upgrading from one version to another.

# Upgrading from 2.x to 3.0

+ **Raised minimum PHP version** to 8.x.  
+ **Added the `json` extension** to Composer requirements, as formatting payloads to JSON is a common use case.  
+ **Removed deprecated methods**:  
  - `_exec()` → Use `exec()`  
  - `setReferrer()` → Use `setReferer()`  
  - `verbose()` → Use `setVerbose()`  