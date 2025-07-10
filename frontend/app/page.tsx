// app/page.tsx
import { ArticleCard } from "@/components/ArticleCard";
import qs from "query-string";
import Pagination from "@/components/Pagination";
import Header from "@/components/Header";

export type Article = {
  id: string;
  title: string;
  url: string;
  source: { name: string };
  description: string;
  published_at: string;
};

interface ArticleResponse {
  data: Article[];
  meta: {
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
  };
}

type FilterOption = { name: string; slug: string };

export default async function Home({
  searchParams,
}: {
  searchParams: Promise<Record<string, string>>;
}) {
  const params = await searchParams;
  const search = params?.search || "";
  const category = params?.category || "";
  const source = params?.source || "";
  const date = params?.date || "";
  const page = params?.page || "1";

  const apiUrl = process.env.API_URL;

  const query = qs.stringify({ q: search, category, source, date, page });
  const [fetchArticle, fetchMetadata] = await Promise.all([
    fetch(`${apiUrl}/articles?${query}`, {
      cache: "no-store",
      headers: {
        Accept: "application/json",
      },
    }),
    fetch(`${apiUrl}/metadata`, {
      cache: "no-store",
      headers: {
        Accept: "application/json",
      },
    }),
  ]);

  const articleResponse: ArticleResponse = await fetchArticle.json();
  const metaData = await fetchMetadata.json();

  const articles: Article[] = articleResponse.data;

  const categories: FilterOption[] = metaData.categories || [];
  const sources: FilterOption[] = metaData.sources || [];

  return (
    <main className="max-w-4xl mx-auto px-4 py-6">
      {await Header()}
      <h1 className="text-2xl font-bold mb-4">Latest News</h1>

      {/* Filter Form */}
      <form method="GET" className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <input
          type="text"
          name="search"
          placeholder="Search articles"
          defaultValue={search}
          className="border px-3 py-2 rounded col-span-1 md:col-span-2"
        />

        <select
          name="category"
          defaultValue={category}
          className="border px-3 py-2 rounded"
        >
          <option value="">All Categories</option>
          {categories.map((cat) => (
            <option key={cat.slug} value={cat.slug}>
              {cat.name}
            </option>
          ))}
        </select>

        <select
          name="source"
          defaultValue={source}
          className="border px-3 py-2 rounded"
        >
          <option value="">All Sources</option>
          {sources.map((src) => (
            <option key={src.slug} value={src.slug}>
              {src.name}
            </option>
          ))}
        </select>

        <input
          type="date"
          name="date"
          defaultValue={date}
          className="border px-3 py-2 rounded"
        />

        <button
          type="submit"
          className="md:col-span-4 bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700"
        >
          Apply Filters
        </button>
      </form>

      <div className="grid gap-4">
        {articles.map((article: Article) => (
          <ArticleCard key={article.id} article={article} />
        ))}
      </div>

      {/* Pagination */}
      <Pagination
        currentPage={articleResponse.meta.current_page}
        totalPages={articleResponse.meta.last_page}
        basePath="/"
        queryParams={{ search, date, source, category, page }}
      />
    </main>
  );
}
