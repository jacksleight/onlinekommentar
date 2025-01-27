<article class="commentary w-full border print:border-0">
  <commentary
    locale="{{ locale }}"
    :commentary="{
      id: '{{ id }}',
      slug: '{{ slug }}',
      title: '{{ title }}',
      doi: '{{ doi }}',
      date: '{{ date format_localized="%d.%m.%Y" }}',
      assigned_editors: [
        {{ assigned_editors }}
          '{{ name }}',
        {{ /assigned_editors }}
      ],
      assigned_authors: [
        {{ assigned_authors }}
          '{{ name }}',
        {{ /assigned_authors }}
      ],
      legal_text: '{{ legal_text | sanitize }}',
      suggested_citation_long: '{{ suggested_citation_long }}',
      suggested_citation_short: '{{ suggested_citation_short }}',
      original_language: '{{ original_language }}',
      locale: '{{ locale }}',
      pdf_commentary_path: '<?= Storage::url('commentaries/pdf/') ?>',
      pdf_commentary_filename: '{{ pdf_commentary:basename }}',
    }"
    :versions="[
      {{ revisions:commentary :id='id' :locale='locale' }}
        {
          id: '{{ unix_timestamp }}',
          timestamp: '{{ unix_timestamp }}',
          label: '{{ human_readable_timestamp }}',
          label_date_only: '{{ human_readable_timestamp_date_only }}'
        },
      {{ /revisions:commentary }}
    ]"
    version-timestamp="{{ versionTimestamp }}">
    <template v-slot:table-of-contents>
      {{ toc }}
    </template>

    <template v-slot:content>
      {{ contentMarkup }}
    </template>
  </commentary>

  {{ if versionComparisonResult }}
    <version-comparison-modal-dialog
      locale="{{ locale }}"
      :commentary="{
        slug: '{{ slug }}',
      }"
      version-timestamp="{{ versionTimestamp }}"
      :open="true">
      <template v-slot:title>
        {{ $t('compare_versions') }}
      </template>
      <template v-slot:body>
        <div class="prose max-w-full version-comparison">
          {{ versionComparisonResult }}
        </div>
      </template>
    </version-comparison-modal-dialog>
  {{ /if }}
</article>