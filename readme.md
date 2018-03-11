# AMS API

## ACCOUNT TYPES:
- ADMIN
- SECRETARY

# APIs

* login - `api/user/login` (POST)
* register - `api/user/register` (POST)
  - accepts (required):
    - firstname
    - middlename (optional)
    - lastname
    - email
    - password
    - confirm_password
    - address
    - city
    - zipCode
    - mobileNo
    - phoneNo
    - country
    - companyName (Required if CLIENT)
    - companyEmail (Required if CLIENT)
    - lineBusiness (Required if CLIENT)
    - companyAddress (Required if CLIENT)
    - companyCity (Required if CLIENT)
    - companyZipCode (Required if CLIENT)
    - companyLandLine (Required if CLIENT)
    - companyCountry (Required if CLIENT)
    - designation (Required if CLIENT)
    - userType

#### Authenticated user can access...

* logout - `api/user/logout` (POST)
* me - `api/getUserLogin` (GET)
* all users - `api/user/all` (GET)
* view user - `api/user/{id}` (GET)
* update user - `api/user/update` (POST)
  - accepts (required):
    - firstname
    - middlename (optional)
    - lastname
    - email
    - oldPassword (CHECK | OPTIONAL)
    - newPassword (OPTIONAL)
    - confirm_password (confirms new password)
    - address
    - city
    - zipCode
    - mobileNo
    - phoneNo
    - country
    - companyName (Required if CLIENT)
    - companyEmail (Required if CLIENT)
    - lineBusiness (Required if CLIENT)
    - companyAddress (Required if CLIENT)
    - companyCity (Required if CLIENT)
    - companyZipCode (Required if CLIENT)
    - companyLandLine (Required if CLIENT)
    - companyCountry (Required if CLIENT)
    - designation (Required if CLIENT)
    - userType
