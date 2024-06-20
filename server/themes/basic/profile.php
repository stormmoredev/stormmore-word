@layout @frontend/layout.php

@if ($profileUpdated === false)
<div class="rounded-md bg-red-50 p-4 mb-5">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">{{ _ Profile photo update failed ! }}</h3>
            <div class="mt-2 text-sm text-red-700">
                {{ _ File is not image or size is too large (max. %s KB) | $maxFileSize }}
            </div>
        </div>
    </div>
</div>
@end

<div class="space-y-10 divide-y divide-gray-900/10">
    <div class="grid grid-cols-1 gap-x-8 gap-y-8 md:grid-cols-3">
        <div class="px-4 sm:px-0">
            <h2 class="text-base font-semibold leading-7 text-gray-900">Profile</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">
                This information will be displayed publicly so be careful what you share.
            </p>
        </div>

        <form action="/profile" method="post"  enctype="multipart/form-data"
              class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
            <div class="px-4 py-6 sm:p-8">
                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="col-span-full">
                        <div class="flex justify-between">
                            <label for="about" class="text-sm font-medium leading-6 text-gray-900">
                                About
                            </label>
                            <div class="hidden text-sm text-gray-900">
                                <span>words:
                                    <span id="about-me-count-words">0</span>
                                    <span>/{{ $settings->profile->aboutMeMaxWords }}</span>
                                </span>
                            </div>
                        </div>

                        <div class="mt-2">
                            <textarea onkeydown="onAboutMeTextChanged(event)"
                                      data-maxwords="{{ $settings->profile->aboutMeMaxWords }}"
                                      id="about" name="about-me" rows="3"
                                      class="block w-full rounded-md border-0
                            py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 px-2
                            placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-sky-500
                            sm:text-sm sm:leading-6">{{ $profile->about_me}}</textarea>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-gray-600">Write a few sentences about yourself.</p>
                    </div>

                    <div class="col-span-full">
                        <label for="photo" class="block text-sm font-medium leading-6 text-gray-900">
                            {{ _ Profile photo }}
                        </label>
                        <div class="mt-2 flex items-center gap-x-3">
                            {{ profile_photo($profile->name, $profile->photo) }}
                            <button type="button"
                                    onclick="openProfilePhotoDialog()"
                                    class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900
                                        shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                {{ _ Change }}
                            </button>
                            <input type="file" accept=".jpg,.jpeg, .png, .bmp"
                                   id="upload-photo"
                                   class="hidden"
                                   name="profile-photo"
                                onchange="changedUploadedProfilePhoto(this)" />
                        </div>
                        <p class="mt-3 text-sm leading-6">
                            <span id="profile-photo-name" class="text-gray-600"></span>
                            <span id="profile-photo-invalid" class="hidden text-red-800">
                                {{ _ Max photo size is %s KB | $maxPhotoSize }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8">
                <button type="submit" class="rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold
                    text-white shadow-sm hover:bg-sky-500">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>