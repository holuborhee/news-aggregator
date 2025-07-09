"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";

interface PreferencesFormProps {
  initialPreferences: {
    categories: string[];
    sources: string[];
    authors: string[];
  };
  meta: {
    categories: { slug: string; name: string }[];
    sources: { slug: string; name: string }[];
    groupedAuthors: {
      source: string;
      authors: { slug: string; name: string; source_slug: string }[];
    };
  };
}

export default function PreferencesForm({
  initialPreferences,
  meta,
}: PreferencesFormProps) {
  const router = useRouter();

  const [selectedCategories, setSelectedCategories] = useState(
    initialPreferences.categories || []
  );
  const [selectedSources, setSelectedSources] = useState(
    initialPreferences.sources || []
  );
  const [selectedAuthors, setSelectedAuthors] = useState(
    initialPreferences.authors || []
  );

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    const res = await fetch("/preferences/update", {
      method: "POST",
      body: JSON.stringify({
        categories: selectedCategories,
        sources: selectedSources,
        authors: selectedAuthors,
      }),
    });

    if (res.ok) {
      router.push("/feeds");
    } else {
      alert("Failed to update preferences");
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      <div>
        <h2 className="font-bold">Categories</h2>
        <div className="flex flex-wrap gap-2">
          {meta.categories.map((cat) => (
            <label key={cat.slug} className="flex items-center gap-2">
              <input
                type="checkbox"
                checked={selectedCategories.includes(cat.slug)}
                onChange={(e) =>
                  setSelectedCategories((prev) =>
                    e.target.checked
                      ? [...prev, cat.slug]
                      : prev.filter((slug) => slug !== cat.slug)
                  )
                }
              />
              {cat.name}
            </label>
          ))}
        </div>
      </div>

      <div>
        <h2 className="font-bold">Sources</h2>
        <div className="flex flex-wrap gap-2">
          {meta.sources.map((source) => (
            <label key={source.slug} className="flex items-center gap-2">
              <input
                type="checkbox"
                checked={selectedSources.includes(source.slug)}
                onChange={(e) =>
                  setSelectedSources((prev) =>
                    e.target.checked
                      ? [...prev, source.slug]
                      : prev.filter((slug) => slug !== source.slug)
                  )
                }
              />
              {source.name}
            </label>
          ))}
        </div>
      </div>

      <div>
        <h2 className="font-bold">Authors (based on selected sources)</h2>
        <div className="flex flex-wrap gap-2 max-h-[200px] overflow-y-auto border p-2">
          {/* {filteredAuthors.map((author) => (
            <label key={author.slug} className="flex items-center gap-2">
              <input
                type="checkbox"
                checked={selectedAuthors.includes(author.slug)}
                onChange={(e) =>
                  setSelectedAuthors((prev) =>
                    e.target.checked
                      ? [...prev, author.slug]
                      : prev.filter((slug) => slug !== author.slug)
                  )
                }
              />
              {author.name}
            </label>
          ))} */}

          {Object.entries(meta.groupedAuthors).map(
            ([sourceSlug, { source, authors: grouped }]: any) => {
              if (!selectedSources.includes(sourceSlug)) return null;
              return (
                <div key={sourceSlug}>
                  <h4 className="font-semibold text-lg mb-1">{source}</h4>
                  <div className="grid grid-cols-2 gap-2">
                    {grouped.map((author: any) => (
                      <label
                        key={author.slug}
                        className="flex items-center space-x-2"
                      >
                        <input
                          type="checkbox"
                          checked={selectedAuthors.includes(author.slug)}
                          onChange={(e) =>
                            setSelectedAuthors((prev) =>
                              e.target.checked
                                ? [...prev, author.slug]
                                : prev.filter((slug) => slug !== author.slug)
                            )
                          }
                        />
                        <span>{author.name}</span>
                      </label>
                    ))}
                  </div>
                </div>
              );
            }
          )}
        </div>
      </div>

      <button
        type="submit"
        className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      >
        Save Preferences
      </button>
    </form>
  );
}
