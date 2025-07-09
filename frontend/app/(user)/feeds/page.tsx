// app/(user)/feeds/page.tsx
import { cookies } from "next/headers";
import Pagination from "@/components/Pagination";

type Article = {
  title: string;
  description: string;
  url: string;
  published_at: string;
  source: { name: string };
};

type FeedsResponse = {
  data: Article[];
  meta: {
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
  };
};

export default async function FeedsPage({
  searchParams,
}: {
  searchParams: { page?: string };
}) {
  const params = await searchParams;
  const cookieStore = await cookies();
  const token = cookieStore.get("token")?.value;
  const page = params.page || "1";

  const apiUrl = process.env.NEXT_PUBLIC_API_URL;

  const res = await fetch(`${apiUrl}/feed?page=${page}`, {
    headers: { Authorization: `Bearer ${token}`, Accept: "application/json" },
    next: { revalidate: 0 },
  });

  const { data: articles = [], meta }: FeedsResponse = await res.json();

  return (
    <div>
      <h1 className="text-2xl font-bold mb-4">Your News Feed</h1>
      <ul className="space-y-4">
        {articles.map((article, i) => (
          <li key={i} className="border p-4 rounded-md">
            <a
              href={article.url}
              target="_blank"
              className="text-lg font-semibold text-blue-600"
            >
              {article.title}
            </a>
            <p className="text-sm text-gray-600">{article.description}</p>
            <p className="text-xs text-gray-500 mt-1">
              Source: {article.source.name} —{" "}
              {new Date(article.published_at).toLocaleDateString()}
            </p>
          </li>
        ))}
      </ul>
      <Pagination
        currentPage={meta.current_page}
        totalPages={meta.last_page}
        basePath="/feeds"
        queryParams={{ page }}
      />
    </div>
  );
}
