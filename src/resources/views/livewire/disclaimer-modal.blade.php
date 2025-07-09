<div>
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-gray-500/75"></div>

                <!-- Modal container -->
                <div class="relative bg-white rounded-lg shadow-xl w-[60vw] max-h-[75vh] overflow-hidden flex flex-col">
                    <!-- Modal content -->
                    <div class="overflow-y-auto p-6">
                        <!-- Icon -->
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>

                        <!-- Title -->
                        <h2 class="text-xl font-bold text-center mb-4">
                            SOFTWARE LICENSE AGREEMENT & DISCLAIMER
                        </h2>

                        <!-- Content container with scroll -->
                        <div class="prose max-w-none overflow-y-auto max-h-[60vh]">
                            <p class="text-primary-600">Since this program could be used to design deep foundation helical piles which support structures that protect human life, it is "CRITICALLY" important that the user fully understands the intended use and capabilities of the program. Only experienced and licensed professional engineers should use this software and the results thereof.</p>

                            <p>The authors of this software have tried to the best of their ability, to combine the principles of soil mechanics and typical analysis processes into the software program code. Regardless of how thoroughly any software is designed and tested errors may and PROBABLY WILL occur, and it is the responsibility of the Licensee or user (designer, engineer, engineer-of-record, etc.) to thoroughly review the results and must take responsibility for the use of the final values and statements prepared by the software. Therefore, this software should be considered only as an aid to performing numerical calculations.</p>

                            <p>The software was developed and owned by Ram Jack. Therefore, you must treat the software in a legal manner like any other copyrighted material. The registered email address and the associated current password should be kept confidential at all times and not be shared with any person within the firm or outside the firm. If more than one person in a particular company desires to use the software then each person should get a unique User ID and Password from Ram Jack to utilize the software.</p>

                            <p>The software may not be reviewed, compared or evaluated for publication in any manner in any publication media without expressed written consent of Ram Jack.</p>

                            <p>While Ram Jack has taken precautions to assure the correctness of the analytical solution and design techniques used in this software, it cannot and does not guarantee its performance, nor can it or does it bear any responsibility for defects or failures in connection with which this software may be used. In no event will Ram Jack, its officers, owners, employees or consultants be liable to anyone for any unfavorable conditions occurring from the use of this software. The user acknowledges and accepts all of the above statements when choosing to use this software.</p>

                            <p>If Ram Jack's owners, employees or consultants are licensed professional engineers there is no relationship between their professional licenses and the software. Such licensed professionals have provided their labor to develop or advise on the software design only and are not performing services in connection with their professional licenses or any legal requirements related to their licenses.</p>

                            <p>Ram Jack warrants that the software will operate but does not warrant that the software will operate error free or without interruption. The user shall not provide duplicated printed documentation, printed electronic documentation or electronic program or documentation files to any person or entity other than employees or consultants of the authorized user with a "need to know" without Ram Jack's written permission.</p>
                        </div>
                    </div>

                    <!-- Footer with button -->
                    <div class="border-t border-gray-200 p-4 bg-gray-50">
                        <button
                            wire:click="acknowledge"
                            type="button"
                            class="w-full px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500"
                        >
                            I agree to the terms and conditions
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
