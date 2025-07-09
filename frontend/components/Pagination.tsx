import Link from "next/link";

interface PaginationProps {
  currentPage: number;
  totalPages: number;
  basePath: string;
  queryParams?: Record<string, string>;
}

export default function Pagination({
  currentPage,
  totalPages,
  basePath,
  queryParams = {},
}: PaginationProps) {
  if (totalPages <= 1) return null;

  const getPageLink = (page: number) => {
    const params = new URLSearchParams({
      ...queryParams,
      page: page.toString(),
    });
    return `${basePath}?${params.toString()}`;
  };

  const prevPage = currentPage > 1 ? currentPage - 1 : null;
  const nextPage = currentPage < totalPages ? currentPage + 1 : null;

  return (
    <div className="flex justify-center items-center gap-2 mt-6">
      {prevPage && (
        <Link
          href={getPageLink(prevPage)}
          className="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
        >
          Previous
        </Link>
      )}

      <span className="text-sm text-gray-600">
        Page {currentPage} of {totalPages}
      </span>

      {nextPage && (
        <Link
          href={getPageLink(nextPage)}
          className="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
        >
          Next
        </Link>
      )}
    </div>
  );
}
