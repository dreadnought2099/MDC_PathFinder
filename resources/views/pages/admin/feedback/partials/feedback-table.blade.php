 <div class="bg-white dark:bg-gray-800 border-2 border-primary shadow-lg rounded-lg overflow-hidden">
     <div class="overflow-x-auto">
         <table class="min-w-full divide-y divide-gray-200">
             <thead class="bg-gray-50 dark:bg-gray-700">
                 <tr>
                     <th class="px-6 py-3 text-left">
                         <input type="checkbox" id="select-all" onclick="toggleSelectAll(this)"
                             class="rounded border-gray-300 text-primary focus:ring-primary">
                     </th>
                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                         Date</th>
                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                         Type</th>
                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                         Rating</th>
                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                         Message</th>
                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                         Score</th>
                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                         Status</th>
                     <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                         Actions</th>
                 </tr>
             </thead>
             <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800">
                 @forelse($feedback as $item)
                     <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                         <td class="px-6 py-4">
                             <input type="checkbox"
                                 class="feedback-checkbox rounded border-gray-300 text-primary focus:ring-primary"
                                 value="{{ $item->id }}" onchange="updateSelectedCount()">
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm dark:text-gray-300">
                             {{ $item->created_at->format('M d, Y H:i') }}
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm">
                             <span
                                 class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded text-xs">
                                 {{ ucfirst($item->feedback_type) }}
                             </span>
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm">
                             @if ($item->rating)
                                 <div class="flex">
                                     @for ($i = 1; $i <= 5; $i++)
                                         <svg class="w-4 h-4 {{ $i <= $item->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
                                             fill="currentColor" viewBox="0 0 20 20">
                                             <path
                                                 d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                         </svg>
                                     @endfor
                                 </div>
                             @else
                                 <span class="text-gray-400 dark:text-gray-500">N/A</span>
                             @endif
                         </td>
                         <td class="px-6 py-4 text-sm dark:text-gray-300" style="max-width: 300px;">
                             <div class="truncate" title="{{ $item->message }}">
                                 {{ $item->message }}
                             </div>
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm">
                             @if ($item->recaptcha_score)
                                 <span
                                     class="px-2 py-1 rounded text-xs {{ $item->recaptcha_score >= 0.7 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ($item->recaptcha_score >= 0.5 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                     {{ number_format($item->recaptcha_score, 2) }}
                                 </span>
                             @endif
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm">
                             <form method="POST" action="{{ route('feedback.updateStatus', $item) }}" class="inline">
                                 @csrf
                                 @method('PATCH')
                                 <select name="status" onchange="this.form.submit()"
                                     class="px-2 py-1 rounded text-xs border-0 cursor-pointer {{ $item->status == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : ($item->status == 'reviewed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : ($item->status == 'resolved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300')) }}">
                                     <option value="pending" {{ $item->status == 'pending' ? 'selected' : '' }}>
                                         Pending</option>
                                     <option value="reviewed" {{ $item->status == 'reviewed' ? 'selected' : '' }}>
                                         Reviewed</option>
                                     <option value="resolved" {{ $item->status == 'resolved' ? 'selected' : '' }}>
                                         Resolved</option>
                                     <option value="archived" {{ $item->status == 'archived' ? 'selected' : '' }}>
                                         Archived</option>
                                 </select>
                             </form>
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                             <div class="relative inline-block group">
                                 <a href="{{ route('feedback.show', $item) }}"
                                     class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover-underline hover:scale-125 ease-in-out transition-all duration-300">
                                     <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/view.png"
                                         alt="View Icon" class="w-8 h-8 object-contain">
                                 </a>
                                 <div
                                     class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap pointer-events-none hidden lg:block">
                                     View Feedback
                                     <div
                                         class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                                     </div>
                                 </div>
                             </div>

                             <div class="relative inline-block group">
                                 <button type="button"
                                     onclick="openFeedbackModal({{ $item->id }}, '{{ ucfirst($item->feedback_type) }}')"
                                     class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover-underline-delete hover:scale-125 ease-in-out transition-all duration-300">
                                     <img src="https://cdn.jsdelivr.net/gh/dreadnought2099/MDC_PathFinder/public/icons/trash.png"
                                         alt="Recycle Bin Icon" class="w-8 h-8 object-contain">
                                 </button>
                                 <div
                                     class="absolute right-full mr-3 top-1/2 -translate-y-1/2 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap pointer-events-none hidden lg:block">
                                     Delete Feedback
                                     <div
                                         class="absolute left-full top-1/2 -translate-y-1/2 w-0 h-0 border-l-4 border-l-gray-900 border-t-4 border-t-transparent border-b-4 border-b-transparent">
                                     </div>
                                 </div>
                             </div>
                         </td>
                     </tr>
                 @empty
                     <tr>
                         <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                             No feedback found
                         </td>
                     </tr>
                 @endforelse
             </tbody>
         </table>
     </div>
 </div>

 <div class="mt-6">
     {{ $feedback->appends(request()->query())->links() }}
 </div>
