# Trainee Progress & Photo Authorization Security Analysis & Fix

**Project:** Captain Mahmoud Fitness Management System  
**Document:** `docs/security/trainee-progress-access.md`  
**Date:** October 2026  
**Scope:** Task 2 — Secure Trainee Progress and Photo Authorization

---

## 1. Overview and Scope

This document details the security investigation, vulnerability verification, code-level remediation, and regression test results for trainee progress check-ins, body measurements, transformation comparison, and progress photos.

### Routes & Endpoints Covered

| HTTP Method | Route Name | URI | Handler |
|---|---|---|---|
| `GET` | `system.trainee-progress.index` | `/system/trainee-progress` | `TraineeProgressController@index` |
| `POST` | `system.trainee-progress.store` | `/system/trainee-progress` | `TraineeProgressController@store` |
| `GET` | `system.trainee-progress.compare` | `/system/trainee-progress/compare` | `TraineeProgressController@compare` |
| `POST` | `system.trainee-progress.update-notes` | `/system/trainee-progress/{id}/notes` | `TraineeProgressController@updateCoachNotes` |
| `DELETE` | `system.trainee-progress.destroy` | `/system/trainee-progress/{id}` | `TraineeProgressController@destroy` |
| `DELETE` | `system.trainee-progress.destroy-photo` | `/system/trainee-progress/photo/{photoId}` | `TraineeProgressController@destroyPhoto` |
| `POST` | *(Unrouted API)* | `/api/trainee/photos/upload` | `TraineePhotoApiController@upload` |

---

## 2. Verified Authorization Architecture

The application distinguishes between user types via `user.user_type`:
* **Admin / Coach / Moderator (`user_type == 1` or `null`):**
  - Super-administrators and staff who manage trainees, assign workouts, formulate nutrition plans, and review check-ins.
  - Authorization is verified either via supervisor permission groups (`is_supervisor == 'yes'`) or route permission lists (`permissions` table).
  - Can view, compare, and upload progress check-ins on behalf of any trainee (e.g. from the admin trainee profile `/system/trainee/{id}`).
* **Trainee (`user_type == 2` or `3`):**
  - Clients logging into their portal to view workouts and track progress.
  - Assigned group `125` and restricted by middleware `Permission.php` to a specific route whitelist (`system.dashboard.trainer`, `system.nutrition.my-plan`, `system.trainee-progress.*`).
  - Must be strictly limited to accessing, viewing, comparing, modifying, and deleting **only their own** progress records and photos.

---

## 3. Vulnerability Findings & Root Cause Analysis

### Confirmed Insecure Direct Object Reference (IDOR) Flaws

Prior to the fix, an audit raised potential IDOR vulnerabilities involving `trainee_id`. Independent code inspection confirmed the following vulnerabilities:

1. **Information Disclosure in `TraineeProgressController@index` (Confirmed IDOR):**
   - **File:** `app/Modules/System/TraineeProgressController.php` (line 39)
   - **Root Cause:** All progress routes were registered in `ignoredRoutes()` in `app/Helpers/Global.helpers.php`, bypassing route permission checks for all authenticated users. In `index()`, if `trainee_id` or `trainee` (encrypted) was supplied, the controller loaded `Trainee::find($request->trainee_id)` without verifying whether the authenticated user owned that trainee record.
   - **Impact:** Any authenticated trainee could view another trainee's complete progress gallery, body weight measurements, dates, personal notes, coach feedback, and photos by simply supplying `?trainee_id={target_id}`.

2. **Information Disclosure in `TraineeProgressController@compare` (Confirmed IDOR):**
   - **File:** `app/Modules/System/TraineeProgressController.php` (lines 183–196)
   - **Root Cause:** `compare()` accepted `trainee_id` directly from the request and queried `getComparisonData((int) $traineeId, $date1, $date2)` with zero ownership or authorization checks.
   - **Impact:** Any logged-in trainee could view transformation photos and side-by-side check-in data of any other trainee.

3. **Unauthorized Write Operations & Privilege Escalation in `store()` (Confirmed):**
   - **File:** `app/Http/Requests/TraineeProgressRequest.php` & `app/Services/TraineeProgressService.php`
   - **Root Cause:** `TraineeProgressRequest::authorize()` returned `true` unconditionally. In `storeCheckin()`, user_type 3 trainees fell into the `elseif (!empty($data['trainee_id']))` branch, allowing them to create check-ins for any trainee. Furthermore, `coach_notes` submitted in the request payload was persisted even when submitted by trainees.
   - **Impact:** Trainees could forge coach evaluation feedback, and user_type 3 accounts could inject check-ins into another trainee's account.

4. **Missing 403 Response & Edge-Case Bypass in Deletions and Coach Notes:**
   - **File:** `app/Modules/System/TraineeProgressController.php` & `app/Services/TraineeProgressService.php`
   - **Root Cause:** `deleteCheckin()` and `deletePhoto()` only checked `user_type == 2` (ignoring `user_type == 3`) and threw generic `\Exception` rather than returning an HTTP 403 status code. If `photo->checkin` was missing, `deletePhoto()` crashed with a 500 error instead of handling ownership safely.

5. **Dormant API Controller Exposure (`TraineePhotoApiController`):**
   - **File:** `app/Modules/Api/TraineePhotoApiController.php`
   - **Root Cause:** The controller lacked authentication enforcement (unauthenticated requests proceeded), accepted arbitrary `trainee_id`, and allowed attaching photos to any `checkin_id` without verifying that the check-in belonged to the specified trainee.

---

## 4. Implemented Fix

A minimal, targeted, and production-safe fix was implemented across four files:

### 1. `app/Modules/System/TraineeProgressController.php`
* **`index()`**: Enforces that trainee accounts (`user_type == 2 || user_type == 3`) are strictly bound to their own trainee profile (`Trainee::where('user_id', $authUser->id)->first()`). If a trainee passes `trainee_id` or encrypted `trainee` pointing to another user/trainee, the request is aborted immediately with `abort(403, __('Unauthorized access to trainee progress.'))`. Privileged coach/admin workflows remain preserved.
* **`compare()`**: Verifies trainee ownership before querying comparison check-ins. Trainees attempting to pass another trainee's ID are rejected with `abort(403)`.
* **`updateCoachNotes()`**: Explicitly blocks trainee accounts from submitting coach feedback (`abort(403)`).
* **`destroy()` & `destroyPhoto()`**: Re-throws `HttpException` so that authorization rejections bubble up to standard HTTP 403 responses.

### 2. `app/Services/TraineeProgressService.php`
* **`resolveTrainee()`**: Accounts for both trainee user types (`user_type == 2` and `user_type == 3`).
* **`storeCheckin()`**: Enforces trainee record resolution from `$authUser->id`. If `trainee_id` is supplied and does not match the authenticated trainee's profile, it aborts with HTTP 403. Trainee submissions automatically have `coach_notes` stripped and `coach_id` set to `null`.
* **`updateCoachNotes()`**: Blocks trainees with HTTP 403.
* **`deleteCheckin()` & `deletePhoto()`**: Validates check-in/photo trainee ownership and aborts with HTTP 403 if ownership does not match.

### 3. `app/Http/Requests/TraineeProgressRequest.php`
* **`authorize()`**: Updated to check authenticated user identity. Rejects (`return false` -> 403) any request where a trainee supplies a `trainee_id` not matching their own profile.

### 4. `app/Modules/Api/TraineePhotoApiController.php`
* Enforces authentication (`return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401)`).
* Restricts trainees to their own profile and returns HTTP 403 if `trainee_id` or `checkin_id` does not belong to the authenticated trainee.

---

## 5. Regression Testing & Verification

A dedicated regression test suite was created in `tests/Feature/TraineeProgressAuthorizationTest.php`.

### Test Environment Isolation
* Runs strictly in **SQLite in-memory** (`:memory:`) as enforced by `phpunit.xml`.
* No queries, connections, or migrations touch MySQL or production databases.
* Synthetic test data only (mock users Alice, Bob, and Coach Mahmoud).
* Public storage faked via `Storage::fake('public')`.

### Verified Test Cases (15/15 Passing)

1. `test_authenticated_trainee_can_access_own_progress`: Authenticated trainee accesses own progress gallery (HTTP 200).
2. `test_trainee_cannot_access_another_trainee_progress_via_trainee_id`: Trainee attempting IDOR via `trainee_id` query parameter is rejected (HTTP 403).
3. `test_trainee_cannot_access_another_trainee_progress_via_encrypted_param`: Trainee attempting IDOR via encrypted `trainee` parameter is rejected (HTTP 403).
4. `test_trainee_cannot_compare_another_trainees_progress`: Trainee attempting side-by-side comparison of another trainee's records is rejected (HTTP 403).
5. `test_trainee_can_compare_own_checkins`: Trainee can compare their own check-ins (HTTP 200).
6. `test_trainee_cannot_submit_progress_checkin_for_another_trainee`: Unauthorized write operation is rejected (HTTP 403) and no record is created in the database.
7. `test_trainee_submitting_checkin_strips_forged_coach_notes`: Check-in is created for the trainee, but forged `coach_notes` is stripped to `null`.
8. `test_trainee_cannot_update_coach_notes`: Trainee attempting to update coach feedback is rejected (HTTP 403).
9. `test_trainee_cannot_delete_another_trainees_checkin`: Deletion of another trainee's check-in is rejected (HTTP 403) and record remains in DB.
10. `test_trainee_can_delete_own_checkin`: Trainee can delete their own check-in session.
11. `test_trainee_cannot_delete_another_trainees_photo`: Deletion of another trainee's photo is rejected (HTTP 403) and photo record remains in DB.
12. `test_trainee_can_delete_own_photo`: Trainee can delete their own photo.
13. `test_administrator_can_access_and_manage_trainee_progress`: Admin/Coach can view, update coach notes, compare, and manage any trainee's progress.
14. `test_unauthenticated_request_is_redirected_to_login`: Unauthenticated requests follow application authentication flow (redirects to login).
15. `test_trainee_photo_api_controller_authorization`: Directly tests `TraineePhotoApiController@upload` rejecting unauthenticated requests (HTTP 401) and tampering with `trainee_id` / `checkin_id` (HTTP 403).
16. `test_trainee_with_user_type_3_is_strictly_authorized`: Verifies that trainees configured with `user_type = 3` are restricted to their own progress, blocked from other trainees' records (HTTP 403), and blocked from modifying coach feedback (HTTP 403).
17. `test_administrator_can_create_checkin_and_delete_photo`: Verifies that administrators can legitimately create check-ins with coach feedback on behalf of trainees and delete check-in photos.

### Execution Command & Results
```bash
php vendor/phpunit/phpunit/phpunit tests/Feature/TraineeProgressAuthorizationTest.php
```
```
Tests: 17, Assertions: 78, Failures: 0. (100% OK)
```
Entire suite:
```bash
php vendor/phpunit/phpunit/phpunit
```
```
Tests: 21, Assertions: 92, Failures: 0. (100% OK)
```

---

## 6. Photo Storage & Direct URL Exposure Analysis

### Current Architecture
* Photo files are processed into WebP format with 75% quality compression and stored on the `public` disk:
  `Storage::disk('public')->put('progress_photos/progress_' . Str::random(16) . '.webp', ...)`
* URLs are generated via `asset('storage/' . $photo->photo_path)`.
* Because `public/storage` symlinks to `storage/app/public`, web servers (Nginx/Apache) serve these files directly as static assets.

### Risk Assessment
* **Unguessable Paths:** Filenames use 16 random characters (`Str::random(16)`), providing ~95 bits of entropy. Without directory indexing, URLs cannot be brute-forced or guessed sequentially.
* **Direct URL Exposure:** If a photo URL is leaked, shared, or retained in browser cache, anyone with the exact URL can view the static image without passing through Laravel controller middleware.
* **Why Storage Config Was Not Altered in Task 2:**
  - Changing disk storage from `public` to a private disk (e.g. `local` or `s3` private) with signed temporary URLs requires moving existing production photo files from `storage/app/public/progress_photos` to private storage.
  - Making this change in code without a coordinated file migration in production would immediately break all existing progress photo images for live users.
  - In accordance with production safety rules, storage paths were preserved, and this enhancement is documented below for coordinated future deployment.

### Recommended Future Hardening (Coordinated Deployment)
When a coordinated deployment is planned:
1. Configure a private storage disk (e.g. `storage/app/private/progress_photos`).
2. Implement a protected streaming route or signed temporary URL generator (`URL::temporarySignedRoute(...)`).
3. Migrate existing files from `storage/app/public/progress_photos` to the private directory.
