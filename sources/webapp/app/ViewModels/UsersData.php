<?php
 
namespace App\ViewModels;

use Statamic\Facades\Entry; 
use Statamic\View\ViewModel;
use Storage;
 
class UsersData extends ViewModel
{
    public function data(): array
    {
        $commentaries = $this->_getCommentariesForUsers('assigned_authors');
        $authors = $this->_getAssignedUsersOfCommentaries($commentaries, 'assigned_authors');
        $authorLegalDomains = $this->_getLegalDomainsOfAssignedUsers($authors);

        $commentaries = $this->_getCommentariesForUsers('assigned_editors');
        $editors = $this->_getAssignedUsersOfCommentaries($commentaries, 'assigned_editors');
        $editorLegalDomains = $this->_getLegalDomainsOfAssignedUsers($editors);

        return [
            'authors' => json_encode(array_values($authors), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT),
            'author_legal_domains' => json_encode(array_values($authorLegalDomains), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT),
            'editors' => json_encode(array_values($editors), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT),
            'editor_legal_domains' => json_encode(array_values($editorLegalDomains), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT)
        ];
    }

    private function _getCommentariesForUsers($userFieldName)
    {
        return Entry::query()
            ->where('collection', 'commentaries')
            ->where('status', 'published')
            ->where('locale', app()->getLocale())
            ->where($userFieldName, '!=', null)
            ->get()
            ->map(function ($commentary, $key) use ($userFieldName) {
                return [
                    'id' => $commentary['id'],
                    $userFieldName => $commentary[$userFieldName]->map(function ($author, $key) {
                        return [
                            'id' => $author['id'],
                            'name' => $author['name'],
                            'email' => $author['email'],
                            'legal_domain' => Entry::query()
                                ->where('collection', 'legal_domains')
                                ->where('id', $author->value('legal_domain'))
                                ->get()
                                ->map(function ($legal_domain, $key) {
                                    return [
                                        'id' => $legal_domain['id'],
                                        'label' => trans($legal_domain['title'])
                                    ];
                                })
                                ->first(),
                            'title' => $author['professional_title'],
                            'occupation' => $author['occupation'],
                            'practice' => $author['practice'],
                            'linkedin_url' => $author['linkedin_url'],
                            'website_url' => $author['website_url'],
                            'avatar' => $author->value('avatar') ? Storage::url('avatars/') . $author->value('avatar') : null
                        ];
                    })->toArray()
                ];
            })->toArray();
    }

    private function _getAssignedUsersOfCommentaries($commentaries, $userFieldName)
    {
        // extract the assigned users from the list of commentaries
        $users = array_column($commentaries, $userFieldName);

        // remove the first level of grouping to create a flattened list of assigned users across all commentaries
        $users = array_merge(...$users);

        // remove duplicate users that have the same id
        $users = array_values(array_intersect_key($users, array_unique(array_column($users, 'id'))));
      
        // sort the users by the label of the legal domain
        usort($users, fn($obj1, $obj2) => strcmp($obj1['legal_domain'] ? $obj1['legal_domain']['label'] : '', $obj2['legal_domain'] ? $obj2['legal_domain']['label'] : ''));

        return $users;
    }

    private function _getLegalDomainsOfAssignedUsers($users)
    {
        // get the non-null, unique legal domains from the list of assigned users
        // reset the array index values to return an indexed array instead of an associative array
        $legalDomains = array_values(array_unique(array_filter(array_column($users, 'legal_domain')), SORT_REGULAR));

        // prepend an option to display all legal domains
        array_unshift($legalDomains, ['id' => null, 'label' => __('legal_domain_filter_label') . ': ' . __('legal_domain_filter_all')]);

        return $legalDomains;
    }
}